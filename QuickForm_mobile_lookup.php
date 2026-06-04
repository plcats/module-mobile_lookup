<?php
/**
 * HTML QuickForm element per mobile_lookup widget.
 * 
 * Renderizza un <select> con attributi data per Select2 initialization.
 */

require_once 'HTML/QuickForm/text.php';

class HTML_QuickForm_mobile_lookup extends HTML_QuickForm_text
{
    // {{{ properties

    var $_options = array();

    // }}}
    // {{{ constructor

    function __construct($elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'mobile_lookup';
        if (isset($options)) {
            $this->loadArray($options);
        }
    }

    function HTML_QuickForm_mobile_lookup($elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }

    function setProperties($properties)
    {
        $this->_properties = $properties;
    }

    function &getProperties()
    {
        return $this->_properties;
    }

    function toHtml()
    {
        $out = '';

        // Carica Select2 CDN e JS/CSS del modulo (una sola volta)
        if (!defined('HTML_QuickForm_mobile_lookup_assets_loaded')) {
            define('HTML_QuickForm_mobile_lookup_assets_loaded', 1);

            // Calcola URL base del modulo
            $app = Dataface_Application::getInstance();
            $siteUrl = DATAFACE_SITE_URL;
            $moduleUrl = $siteUrl . '/modules/mobile_lookup';

            $out .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />';
            $out .= '<link rel="stylesheet" href="' . $moduleUrl . '/css/mobile-lookup.css" />';
            $out .= '<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
            $out .= '<script src="' . $moduleUrl . '/js/mobile-lookup.js"></script>';
        }

        // Aggiungi attributi data- per Select2 JS initialization
        $props = $this->_properties;

        // Passa tutte le opzioni come JSON per il JS
        $jsOptions = array();
        foreach (array('table', 'keycol', 'labelcol', 'searchFields', 'placeholder', 'minimumInputLength', 'filters', 'allscreen', 'canEdit', 'canNew', 'editFields', 'image', 'maxWidth') as $key) {
            if (isset($props[$key])) {
                $jsOptions[$key] = $props[$key];
            }
        }

        // Pass-through di widget:atts:* (es. widget:atts:maxWidth)
        if (isset($props['atts']) && is_array($props['atts'])) {
            $jsOptions['atts'] = $props['atts'];
        }
        foreach ($props as $k => $v) {
            if (strpos($k, 'atts:') === 0) {
                $attKey = substr($k, 5);
                if ($attKey !== '' && (is_string($v) || is_numeric($v) || is_bool($v))) {
                    if (!isset($jsOptions['atts']) || !is_array($jsOptions['atts'])) {
                        $jsOptions['atts'] = array();
                    }
                    $jsOptions['atts'][$attKey] = $v;
                }
            }
        }

        $cssClass = 'xf-mobile-lookup';
        $this->updateAttributes(array(
            'class' => $cssClass,
            'data-xf-mobile-lookup' => '1',
            'data-mobile-lookup-options' => json_encode($jsOptions),
            'data-placeholder' => isset($props['placeholder']) ? $props['placeholder'] : 'Seleziona...',
            'data-allow-clear' => 'true'
        ));

        // Aggiungi opzione vuota come primo elemento per il placeholder
        $hasEmpty = false;
        foreach ($this->_options as $opt) {
            if ($opt['attr']['value'] === '' || $opt['attr']['value'] === null) {
                $hasEmpty = true;
                break;
            }
        }
        if (!$hasEmpty) {
            array_unshift($this->_options, array('text' => '', 'attr' => array('value' => '')));
        }

        if ($this->_flagFrozen) {
            return $out . $this->getFrozenHtml();
        }

        // Generate the <select> element manually, as parent::toHtml() would generate an <input type="text">
        $tabs = $this->_getTabs();
        $strHtml = '';
        if ($this->getComment() != '') {
            $strHtml .= $tabs . '<!-- ' . $this->getComment() . " -->\n";
        }
        $strHtml .= $tabs . '<select' . $this->_getAttrString($this->_attributes) . '>';
        foreach ($this->_options as $option) {
            $strHtml .= $tabs . "\t<option";
            if (isset($option['attr']) && is_array($option['attr'])) {
                foreach ($option['attr'] as $key => $value) {
                    $strHtml .= ' ' . $key . '="' . $value . '"';
                }
            }
            $currentValue = $this->getValue();
            if ($currentValue === (string) $option['attr']['value']) {
                $strHtml .= ' selected="selected"';
            }
            $strHtml .= '>' . $option['text'] . "</option>\n";
        }
        $strHtml .= $tabs . '</select>';

        // Wrap con pulsanti modifica/nuovo se abilitati
        $canEdit = !empty($props['canEdit']) ? $props['canEdit'] : 0;
        $canNew  = !empty($props['canNew'])  ? $props['canNew']  : 0;

        $editIcon = '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
            . '<path fill="currentColor" d="M3 17.25V21h3.75l11-11-3.75-3.75-11 11zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/>'
            . '</svg>';
        $newIcon = '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
            . '<path fill="currentColor" d="M19 11H13V5h-2v6H5v2h6v6h2v-6h6z"/>'
            . '</svg>';

        $editBtn = '<a href="#" class="xf-mobile-lookup-edit' . ($canEdit ? '' : ' is-disabled') . '" title="Modifica selezione" aria-label="Modifica selezione">'
            . $editIcon
            . '</a>';
        $newBtn  = '<a href="#" class="xf-mobile-lookup-new'  . ($canNew  ? '' : ' is-disabled') . '" title="Nuovo record" aria-label="Nuovo record">'
            . $newIcon
            . '</a>';

        $strHtml = $tabs . '<div class="xf-mobile-lookup-wrapper" style="display:flex;gap:6px;align-items:center">'
            . $strHtml
            . ($canEdit || $canNew ? '<span class="xf-mobile-lookup-actions">' . $editBtn . ' ' . $newBtn . '</span>' : '')
            . '</div>';

        return $out . $strHtml;
    }

    function getFrozenHtml()
    {
        $value = $this->getValue();
        return ('' != $value ? htmlspecialchars($value) : '&nbsp;') .
            $this->_getPersistantData();
    }

    /**
     * Carica le opzioni iniziali se passate (utile per il placeholder o per il valore corrente)
     */
    function loadArray($arr)
    {
        if (!is_array($arr))
            return PEAR::raiseError('Argument is not a valid array');
        foreach ($arr as $key => $val) {
            $this->_options[] = array('text' => $val, 'attr' => array('value' => $key));
        }
        return true;
    }

}
