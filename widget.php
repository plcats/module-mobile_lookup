<?php
/**
 * FormTool handler per widget mobile_lookup
 * 
 * Gestisce la costruzione del widget mobile_lookup per campi in fields.ini
 */

$GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['mobile_lookup'] = array(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'QuickForm_mobile_lookup.php',
    'HTML_QuickForm_mobile_lookup'
);

class Dataface_FormTool_mobile_lookup
{

    /**
     * Risolve il nome base del campo record anche in contesti grid
     * (es. fkArticolo[0] -> fkArticolo).
     */
    private function resolveBaseFieldName($fieldName)
    {
        if (!is_scalar($fieldName)) {
            return '';
        }
        $name = trim((string) $fieldName);
        if ($name === '') {
            return '';
        }
        $bracketPos = strpos($name, '[');
        if ($bracketPos !== false) {
            $name = substr($name, 0, $bracketPos);
        }
        return $name;
    }

    /**
     * Costruisce il widget mobile_lookup
     * 
     * @param Dataface_Record $record Record corrente
     * @param array $field Definizione campo da fields.ini
     * @param HTML_QuickForm $form Form oggetto
     * @param string $formFieldName Nome del campo nel form
     * @param bool $new Se è un nuovo record
     * @return HTML_QuickForm_mobile_lookup Widget element
     */
    function &buildWidget(&$record, &$field, &$form, $formFieldName, $new = false)
    {
        static $tableMetaCache = array();
        static $seedOptionsCache = array();
        static $currentValueLabelCache = array();

        $widget =& $field['widget'];
        $factory =& Dataface_FormTool::factory();

        $tableNameFromWidget = (isset($widget['table']) && is_scalar($widget['table'])) ? trim((string) $widget['table']) : '';
        $hasLookupTable = ($tableNameFromWidget !== '');
        $requestedPreloadRaw = (isset($widget['preloadOptions']) && is_scalar($widget['preloadOptions'])) ? (string) $widget['preloadOptions'] : 'selected';
        $requestedPreloadMode = strtolower(trim($requestedPreloadRaw));
        if (!in_array($requestedPreloadMode, array('selected', 'first100', 'none'))) {
            $requestedPreloadMode = 'selected';
        }

        // Prima ottieni le opzioni dal vocabulary per popolare il select
        $options = array();
        // Se il campo usa lookup su tabella, ignora sempre la vocabulary:
        // il rendering/ricerca deve dipendere solo da keycol/labelcol/titleColumn.
        $shouldLoadVocabulary = isset($field['vocabulary']) && !$hasLookupTable;
        if ($shouldLoadVocabulary) {
            // Ottieni la tabella corrente dal record
            $table =& Dataface_Table::loadTable($record->_table->tablename);
            if (!PEAR::isError($table)) {
                // Usa il metodo corretto per ottenere valuelist
                $valuelist = $table->getValuelist($field['vocabulary']);
                if ($valuelist) {
                    $options = $valuelist;
                }
            }
        }

        $currentFieldName = isset($field['name']) ? $field['name'] : $formFieldName;
        $currentFieldBase = $this->resolveBaseFieldName($currentFieldName);
        $currentValue = '';
        if ($currentFieldBase !== '') {
            $rawCurrentValue = $record->val($currentFieldBase);
            if ($rawCurrentValue !== null) {
                $currentValue = is_scalar($rawCurrentValue) ? (string) $rawCurrentValue : '';
            }
            if ($currentValue === '') {
                $currentValue = $record->strval($currentFieldBase);
            }
        }

        if ($hasLookupTable) {
            // Se non c'è vocabulary, costruisci opzioni dalla tabella target
            // Supporta labelcol multipli o usa title del record
            $targetTable = $tableNameFromWidget;
            $keyCol = isset($widget['keycol']) ? $widget['keycol'] : '';
            $labelCol = isset($widget['labelcol']) ? $widget['labelcol'] : '';
            $labelColExpression = '';
            $useLabelExpression = false;
            $tableMetaKey = $targetTable;

            // Strategia preload per ridurre overhead in grid:
            // - selected (default): carica solo la label del valore corrente
            // - first100: carica prime 100 opzioni + valore corrente
            // - none: non carica opzioni iniziali
            $preloadMode = $requestedPreloadMode;
            if (!in_array($preloadMode, array('selected', 'first100', 'none'))) {
                $preloadMode = 'selected';
            }

            $targetTableObj = null;
            if (!isset($tableMetaCache[$tableMetaKey])) {
                $tableMetaCache[$tableMetaKey] = array(
                    'loaded' => false,
                    'obj' => null,
                    'defaultKey' => 'id',
                    'canEdit' => 0,
                    'canNew' => 0,
                    'titleColumn' => null,
                    'tableSql' => ''
                );

                try {
                    $targetTableObj = Dataface_Table::loadTable($targetTable);
                    $keys = $targetTableObj->keys();
                    $keyNames = array_keys($keys);
                    $defaultKey = !empty($keyNames) ? $keyNames[0] : 'id';
                    $tablePerms = $targetTableObj->getPermissions();

                    $tableMetaCache[$tableMetaKey] = array(
                        'loaded' => true,
                        'obj' => $targetTableObj,
                        'defaultKey' => $defaultKey,
                        'canEdit' => !empty($tablePerms['edit']) ? 1 : 0,
                        'canNew' => !empty($tablePerms['new']) ? 1 : 0,
                        'titleColumn' => $targetTableObj->titleColumn(),
                        'tableSql' => $targetTableObj->sql()
                    );
                } catch (Exception $e) {
                    // Keep safe defaults.
                }
            }

            $meta = $tableMetaCache[$tableMetaKey];
            if (empty($keyCol)) {
                $keyCol = $meta['defaultKey'];
                $widget['keycol'] = $keyCol;
            }

            // Calcola permessi per pulsanti modifica/nuovo se non forzati in fields.ini.
            if (!isset($widget['canEdit']) || $widget['canEdit'] === '') {
                $widget['canEdit'] = $meta['canEdit'];
            }
            if (!isset($widget['canNew']) || $widget['canNew'] === '') {
                $widget['canNew'] = $meta['canNew'];
            }

            $targetTableObj = $meta['obj'];

            $needsSeedOptions = ($preloadMode === 'first100');
            // Anche con preload=none va mantenuta la label del valore corrente,
            // altrimenti in edit/grid i record gia' selezionati risultano vuoti.
            $needsCurrentValueLabel = ($currentValue !== null && $currentValue !== '');

            if ($needsSeedOptions || $needsCurrentValueLabel) {
                $link = df_db();

                if ($link) {
                    $tableSql = $meta['tableSql'];
                    $fromClause = !empty($tableSql) ? "($tableSql) AS __xf_lookup" : "`$targetTable`";

                    // Se labelcol è vuoto, usa il title del record
                    if (empty($labelCol)) {
                        if (!empty($meta['titleColumn'])) {
                            $labelColExpression = $meta['titleColumn'];
                            $useLabelExpression = true;
                        } else {
                            $labelCol = 'id';
                        }
                    }

                    $seedCacheKey = $targetTable . '|' . $keyCol . '|' . $labelCol . '|' . ($useLabelExpression ? 'title' : 'cols');

                    if ($needsSeedOptions) {
                        if (!isset($seedOptionsCache[$seedCacheKey])) {
                            $seedOptionsCache[$seedCacheKey] = array();

                            if ($useLabelExpression) {
                                $keySafe = preg_match('/^[a-zA-Z0-9_]+$/', $keyCol) ? "`$keyCol`" : '`id`';
                                $labelSelect = "($labelColExpression) as label";
                                $sql = "SELECT $keySafe as keyval, $labelSelect FROM $fromClause ORDER BY $keySafe LIMIT 100";
                                $result = mysqli_query($link, $sql);
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $seedOptionsCache[$seedCacheKey][$row['keyval']] = $row['label'];
                                    }
                                    mysqli_free_result($result);
                                }
                            } else {
                                // Supporto labelcol multipli
                                $labelCols = array_map('trim', explode(',', $labelCol));
                                $labelColsSafe = array();
                                foreach ($labelCols as $col) {
                                    if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                                        $labelColsSafe[] = "`$col`";
                                    }
                                }

                                if (!empty($labelColsSafe)) {
                                    $keySafe = preg_match('/^[a-zA-Z0-9_]+$/', $keyCol) ? "`$keyCol`" : '`id`';

                                    if (count($labelColsSafe) > 1) {
                                        $labelSelect = "CONCAT_WS(' - ', " . implode(', ', $labelColsSafe) . ") as label";
                                    } else {
                                        $labelSelect = "$labelColsSafe[0] as label";
                                    }

                                    $sql = "SELECT $keySafe as keyval, $labelSelect FROM $fromClause ORDER BY $labelColsSafe[0] LIMIT 100";
                                    $result = mysqli_query($link, $sql);

                                    if ($result) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $seedOptionsCache[$seedCacheKey][$row['keyval']] = $row['label'];
                                        }
                                        mysqli_free_result($result);
                                    }
                                }
                            }
                        }

                        if (!empty($seedOptionsCache[$seedCacheKey])) {
                            $options = $options + $seedOptionsCache[$seedCacheKey];
                        }
                    }

                    // Se il valore corrente non è presente, aggiungilo
                    if ($needsCurrentValueLabel && !array_key_exists($currentValue, $options)) {
                        $currentLabelCacheKey = $seedCacheKey . '|val|' . $currentValue;

                        if (!isset($currentValueLabelCache[$currentLabelCacheKey])) {
                            if ($useLabelExpression) {
                                $labelSelect = $labelColExpression;
                            } else {
                                $labelCols = array_map('trim', explode(',', $labelCol));
                                $labelColsSafe = array();
                                foreach ($labelCols as $col) {
                                    if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                                        $labelColsSafe[] = "`$col`";
                                    }
                                }
                                if (count($labelColsSafe) > 1) {
                                    $labelSelect = "CONCAT_WS(' - ', " . implode(', ', $labelColsSafe) . ")";
                                } else if (!empty($labelColsSafe)) {
                                    $labelSelect = $labelColsSafe[0];
                                } else {
                                    $labelSelect = $keyCol;
                                }
                            }

                            $keySafe = preg_match('/^[a-zA-Z0-9_]+$/', $keyCol) ? "`$keyCol`" : '`id`';
                            $sqlSingle = "SELECT $labelSelect as label FROM $fromClause WHERE $keySafe = ? LIMIT 1";
                            $stmt = mysqli_prepare($link, $sqlSingle);
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 's', $currentValue);
                                mysqli_stmt_execute($stmt);
                                $res = mysqli_stmt_get_result($stmt);
                                $row = $res ? mysqli_fetch_assoc($res) : null;
                                mysqli_stmt_close($stmt);
                                $currentValueLabelCache[$currentLabelCacheKey] = $row && isset($row['label']) ? $row['label'] : $currentValue;
                            } else {
                                $currentValueLabelCache[$currentLabelCacheKey] = $currentValue;
                            }
                        }

                        $options[$currentValue] = $currentValueLabelCache[$currentLabelCacheKey];
                    }

                }
            }
        }

        // Crea elemento custom mobile_lookup con le opzioni
        $widgetLabel = isset($widget['label']) ? $widget['label'] : $formFieldName;
        $el =& $factory->addElement('mobile_lookup', $formFieldName, $widgetLabel, $options);

        if (PEAR::isError($el)) {
            return $el;
        }

        // Imposta proprietà widget da fields.ini
        $el->setProperties($widget);

        return $el;
    }

    /**
     * Estrae il valore dal record per popolare il form (record -> form).
     * In Xataface questo metodo si chiama pullValue.
     */
    function pullValue(&$record, &$field, &$form, &$element, $new = false)
    {
        $fieldName = isset($field['name']) ? $field['name'] : $element->getName();
        $fieldBase = $this->resolveBaseFieldName($fieldName);
        $val = '';
        if ($fieldBase !== '') {
            $rawVal = $record->val($fieldBase);
            if ($rawVal !== null) {
                $val = is_scalar($rawVal) ? (string) $rawVal : '';
            }
            if ($val === '') {
                $val = $record->strval($fieldBase);
            }
        }
        $element->setValue($val);
        return $val;
    }

    /**
     * Estrae il valore dal form (widget) per salvarlo nel record (form -> record).
     * In Xataface questo metodo si chiama pushValue.
     */
    function pushValue(&$record, &$field, &$form, &$element, $new = false)
    {
        $val = $element->getValue();
        // Se è un array (come può succedere con select in POST ma trattata come text), prendi il primo valore
        if (is_array($val)) {
            $val = reset($val);
        }
        return $val;
    }
}
