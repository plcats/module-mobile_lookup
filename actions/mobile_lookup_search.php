<?php
/**
 * Action handler per mobile_lookup_search.
 * Risponde a richieste AJAX di ricerca per il widget mobile_lookup.
 * Restituisce JSON con risultati paginati.
 */
class actions_mobile_lookup_search
{

    function handle(&$params)
    {
        header('Content-Type: application/json; charset=utf-8');

        $app = Dataface_Application::getInstance();

        // Parametri dalla richiesta
        $tableName = @$_GET['-table'] ?: '';
        $keyCol = @$_GET['-key'] ?: 'id';
        $labelCol = @$_GET['-label'] ?: '';
        $imageCol = @$_GET['-image'] ?: '';
        $searchTerm = @$_GET['-search'] ?: '';
        $searchFields = @$_GET['-searchFields'] ?: $labelCol;
        $filtersJson = @$_GET['-filters'] ?: '{}';
        $page = intval(@$_GET['-page'] ?: 1);
        $perPage = 30;
        $anchor = intval(@$_GET['-anchor'] ?: 0);
        $selectedVal = @$_GET['-selected'] ?: '';

        if (empty($tableName)) {
            echo json_encode(array('results' => array(), 'total_count' => 0));
            return;
        }

        // Validazione nome tabella
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            echo json_encode(array('results' => array(), 'total_count' => 0));
            return;
        }

        // Validazione colonne
        if (!preg_match('/^[a-zA-Z0-9_,]+$/', $keyCol))
            $keyCol = 'id';

        // Connessione DB nativa Xataface
        $link = df_db();

        if (!$link) {
            echo json_encode(array('results' => array(), 'total_count' => 0, 'error' => 'DB connection failed'));
            return;
        }

        // Carica tabella per ottenere titleColumn e SQL (supporto viste)
        $targetTableObj = null;
        try {
            $targetTableObj = Dataface_Table::loadTable($tableName);
        } catch (Exception $e) {
            // fallback
        }

        // Determina la FROM clause (supporta viste Xataface)
        $fromClause = "`$tableName`";
        if ($targetTableObj) {
            $tableSql = $targetTableObj->sql();
            if (!empty($tableSql)) {
                $fromClause = "($tableSql) AS __xf_lookup";
            }
        }

        // Determina label column
        $labelSelect = '';
        $useTitleColumn = false;
        if (empty($labelCol) && $targetTableObj) {
            $titleColumn = $targetTableObj->titleColumn();
            if ($titleColumn) {
                $labelSelect = "($titleColumn) as label";
                $useTitleColumn = true;
            }
        }

        if (!$useTitleColumn) {
            if (empty($labelCol))
                $labelCol = $keyCol;
            // Supporta labelcol multipli
            $labelCols = array_map('trim', explode(',', $labelCol));
            $labelColsSafe = array();
            foreach ($labelCols as $col) {
                if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                    $labelColsSafe[] = "`$col`";
                }
            }
            if (count($labelColsSafe) > 1) {
                $labelSelect = "CONCAT_WS(' - ', " . implode(', ', $labelColsSafe) . ") as label";
            } else if (!empty($labelColsSafe)) {
                $labelSelect = "$labelColsSafe[0] as label";
            } else {
                $labelSelect = "`$keyCol` as label";
            }
        }

        $keySafe = "`$keyCol`";

        // Colonna immagine
        $imageSelect = '';
        if (!empty($imageCol) && preg_match('/^[a-zA-Z0-9_]+$/', $imageCol)) {
            $imageSelect = ", `$imageCol` as image";
        }

        // Condizioni WHERE
        $conditions = array();

        // Ricerca testuale
        if (!empty($searchTerm)) {
            $searchFieldsArr = array_map('trim', explode(',', $searchFields));
            $searchConditions = array();
            $escapedTerm = mysqli_real_escape_string($link, $searchTerm);
            foreach ($searchFieldsArr as $sf) {
                if (preg_match('/^[a-zA-Z0-9_]+$/', $sf)) {
                    $searchConditions[] = "`$sf` LIKE '%$escapedTerm%'";
                }
            }
            if (!empty($searchConditions)) {
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
            }
        }

        // Filtri custom
        $filters = @json_decode($filtersJson, true);
        if (is_array($filters)) {
            foreach ($filters as $fKey => $fVal) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $fKey))
                    continue;
                if ($fVal === '' || $fVal === null)
                    continue;

                // Supporta ! per diverso da
                if (substr($fVal, 0, 1) === '!') {
                    $escapedVal = mysqli_real_escape_string($link, substr($fVal, 1));
                    $conditions[] = "`$fKey` != '$escapedVal'";
                } else {
                    $escapedVal = mysqli_real_escape_string($link, $fVal);
                    $conditions[] = "`$fKey` = '$escapedVal'";
                }
            }
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Conta totale
        $countSql = "SELECT COUNT(*) as cnt FROM $fromClause $whereClause";
        $countResult = mysqli_query($link, $countSql);
        $totalCount = 0;
        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $totalCount = intval($countRow['cnt']);
            mysqli_free_result($countResult);
        }

        // Calcola offset
        $offset = ($page - 1) * $perPage;

        // Query principale
        $orderBy = $useTitleColumn ? "label" : $keySafe;
        $sql = "SELECT $keySafe as id, $labelSelect $imageSelect FROM $fromClause $whereClause ORDER BY $orderBy LIMIT $perPage OFFSET $offset";

        $result = mysqli_query($link, $sql);
        $results = array();

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $item = array(
                    'id' => $row['id'],
                    'text' => isset($row['label']) ? $row['label'] : $row['id']
                );
                if (isset($row['image'])) {
                    $item['image'] = $row['image'];
                }
                $results[] = $item;
            }
            mysqli_free_result($result);
        }

        // Anchor mode: quando si apre la dropdown senza ricerca, includi il valore selezionato
        // nei risultati per renderlo subito visibile anche se non cade nella prima pagina ordinata.
        if ($anchor && $selectedVal !== '' && empty($searchTerm)) {
            $alreadyPresent = false;
            foreach ($results as $r) {
                if (isset($r['id']) && (string)$r['id'] === (string)$selectedVal) {
                    $alreadyPresent = true;
                    break;
                }
            }

            if (!$alreadyPresent) {
                $selectedLabel = null;
                $selectedImage = null;

                $selectedSql = "SELECT $keySafe as id, $labelSelect $imageSelect FROM $fromClause WHERE $keySafe = ? LIMIT 1";
                $stmt = mysqli_prepare($link, $selectedSql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 's', $selectedVal);
                    mysqli_stmt_execute($stmt);
                    $selRes = mysqli_stmt_get_result($stmt);
                    $selRow = $selRes ? mysqli_fetch_assoc($selRes) : null;
                    mysqli_stmt_close($stmt);

                    if ($selRow) {
                        $selectedLabel = isset($selRow['label']) ? $selRow['label'] : $selectedVal;
                        if (isset($selRow['image'])) {
                            $selectedImage = $selRow['image'];
                        }
                    }
                }

                if ($selectedLabel === null) {
                    $selectedLabel = $selectedVal;
                }

                $selectedItem = array(
                    'id' => $selectedVal,
                    'text' => $selectedLabel
                );
                if ($selectedImage !== null) {
                    $selectedItem['image'] = $selectedImage;
                }

                array_unshift($results, $selectedItem);
            }
        }

        echo json_encode(array(
            'results' => $results,
            'total_count' => $totalCount
        ));

        exit;
    }
}
