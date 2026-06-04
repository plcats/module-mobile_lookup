<?php
/**
 * HTML QuickForm mobile_lookup element
 * 
 * Widget mobile-friendly per lookup record con Select2
 * Ottimizzato per touch screen e dispositivi mobili
 * 
 * File autocontenuto nel modulo modules/mobile_lookup.
 * NON modificare xataface/HTML/QuickForm/ - questo modulo è indipendente.
 * 
 * @author Paolo Bonzini
 * @version 1.0.0
 */

require_once 'HTML/QuickForm/select.php';

/**
 * Classe HTML QuickForm per widget mobile_lookup
 */
class HTML_QuickForm_mobile_lookup extends HTML_QuickForm_select {
    
    /**
     * Index univoco per ogni istanza widget
     */
    var $index;
    
    /**
     * Prefix timestamp per identificazione
     */
    var $index_prefix;
    
    /**
     * Proprietà configurazione widget
     */
    var $_properties = array();
    
    /**
     * Costruttore
     */
    function __construct($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        static $index = 1;
        $this->index = $index++;
        $this->index_prefix = time();
        
        if (!isset($attributes)) {
            $attributes = array();
        }
        
        // Aggiungi classi CSS per identificazione
        $class = @$attributes['class'];
        $class .= ' xf-mobile-lookup xf-mobile-lookup-' . $this->index_prefix . '-' . $this->index;
        $attributes['class'] = trim($class);
        $attributes['df:cloneable'] = 1;
        
        parent::HTML_QuickForm_select($elementName, $elementLabel, $options, $attributes);
        $this->_type = 'mobile_lookup';
    }
    
    /**
     * Costruttore PHP4 compatibility
     */
    function HTML_QuickForm_mobile_lookup($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }
    
    /**
     * Imposta proprietà widget da fields.ini
     */
    function setProperties($properties) {
        $this->_properties = $properties;
    }
    
    /**
     * Ottieni proprietà widget
     * Ritorna per riferimento per compatibilità con HTML_QuickForm_element
     */
    function &getProperties() {
        if (!isset($this->_properties)) {
            $this->_properties = array();
        }
        return $this->_properties;
    }
    
    /**
     * Renderizza widget in HTML
     */
    function toHtml() {
        
        // Ottieni URL modulo
        static $moduleURL = null;
        static $resourcesIncluded = false;
        
        if ($moduleURL === null) {
            $mt = Dataface_ModuleTool::getInstance();
            $mod = $mt->loadModule('modules_mobile_lookup');
            if ($mod) {
                $moduleURL = $mod->getBaseURL();
            }
        }
        
        $properties = $this->getProperties();
        
        // Aggiungi proprietà frozen se necessario
        if ($this->_flagFrozen) {
            $properties['frozen'] = 1;
        }
        
        // Configurazione default
        if (!isset($properties['placeholder'])) {
            $properties['placeholder'] = 'Seleziona...';
        }
        
        // Filtra solo proprietà serializzabili per JavaScript
        $jsProperties = array();
        $allowedKeys = array('table', 'keycol', 'labelcol', 'searchFields', 'placeholder', 'useTitle', 'editFields', 'image', 'allscreen', 'maxWidth');
        foreach ($allowedKeys as $key) {
            if (isset($properties[$key]) && (is_string($properties[$key]) || is_numeric($properties[$key]) || is_bool($properties[$key]))) {
                $jsProperties[$key] = $properties[$key];
            }
        }

        // Pass-through di widget:atts:* (es. widget:atts:maxWidth)
        if (isset($properties['atts']) && is_array($properties['atts'])) {
            $jsProperties['atts'] = $properties['atts'];
        }
        foreach ($properties as $k => $v) {
            if (strpos($k, 'atts:') === 0) {
                $attKey = substr($k, 5);
                if ($attKey !== '' && (is_string($v) || is_numeric($v) || is_bool($v))) {
                    if (!isset($jsProperties['atts']) || !is_array($jsProperties['atts'])) {
                        $jsProperties['atts'] = array();
                    }
                    $jsProperties['atts'][$attKey] = $v;
                }
            }
        }

        // Permessi edit per il tasto modifica
        if (isset($properties['table'])) {
            $targetTable = Dataface_Table::loadTable($properties['table']);
            if (!PEAR::isError($targetTable)) {
                $perms = $targetTable->getPermissions();
                $jsProperties['canEdit'] = !empty($perms['edit']);
                $jsProperties['canNew'] = !empty($perms['new']);
            }
        }
        
        // Estrai filtri - Xataface aggrega widget:filters:* nella chiave 'filters'
        $filters = array();
        
        // Prima prova: chiave 'filters' già aggregata da Xataface
        if (isset($properties['filters']) && is_array($properties['filters'])) {
            $filters = $properties['filters'];
        } else {
            // Fallback: cerca filters:* manualmente
            foreach ($properties as $key => $value) {
                if (strpos($key, 'filters:') === 0) {
                    $filterKey = substr($key, 8); // Rimuovi 'filters:' prefix
                    $filters[$filterKey] = $value;
                }
            }
        }

        if (!empty($filters)) {
            $jsProperties['filters'] = $filters;
        }
        
        // Serializza opzioni per Javascript (json_encode già escapa correttamente, non serve htmlspecialchars)
        $dataOptions = json_encode($jsProperties);
        
        // Aggiorna attributi
        $oldFrozen = $this->_flagFrozen;
        $this->_flagFrozen = 0;
        $this->updateAttributes(array(
            'data-mobile-lookup-options' => $dataOptions
        ));
        
        // Genera HTML select standard
        $out = parent::toHtml();
        
        $this->_flagFrozen = $oldFrozen;
        
        // Include risorse solo una volta
        $resources = '';
        if (!$resourcesIncluded && $moduleURL) {
            $resourcesIncluded = true;
            $resources = '
<link rel="stylesheet" href="' . htmlspecialchars($moduleURL) . '/css/mobile-lookup.css">
    <script src="' . htmlspecialchars(DATAFACE_URL) . '/js/xataface/lang.js"></script>
    <script src="' . htmlspecialchars(DATAFACE_URL) . '/js/RecordDialog/RecordDialog.js"></script>
<script src="' . htmlspecialchars($moduleURL) . '/js/mobile-lookup.js"></script>
';
        }
        
        $editButton = '<a class="xf-mobile-lookup-edit" href="#" title="Modifica voce selezionata" aria-label="Modifica voce selezionata">✎</a>';
        $newButton = '<a class="xf-mobile-lookup-new" href="#" title="Nuovo" aria-label="Nuovo">+</a>';
        return $resources . '<span class="xf-mobile-lookup-wrapper">' . $out . '<span class="xf-mobile-lookup-actions">' . $editButton . $newButton . '</span></span>';
    }
    
}
