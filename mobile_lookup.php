<?php
/**
 * Mobile Lookup Module for Xataface
 * 
 * Provides a mobile-friendly lookup widget using Select2 library
 * for touch-optimized record selection with search capabilities.
 * 
 * @author Paolo Bonzini
 * @version 1.0.0
 * @created February 5, 2026
 * 
 * Usage in fields.ini:
 *   widget:type=mobile_lookup
 *   widget:table=TableName
 *   widget:keycol=id
 *   widget:labelcol=nome
 *   widget:searchFields=nome,codice
 *   widget:placeholder="Seleziona record..."
 */

class modules_mobile_lookup {

    /**
     * Base URL del modulo
     */
    private $baseURL = null;
    
    /**
     * Inizializza il modulo e registra i widget
     */
    function __construct() {
        $app = Dataface_Application::getInstance();
        
        // Carica dipendenze moduli
        $mt = Dataface_ModuleTool::getInstance();
        
        // XataJax per Javascript/CSS tools
        $mt->loadModule('modules_XataJax', 'modules/XataJax/XataJax.php');
        
        // Registra il widget mobile_lookup con FormTool
        import('Dataface/FormTool.php');
        $ft = Dataface_FormTool::getInstance();
        $ft->registerWidgetHandler(
            'mobile_lookup', 
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'widget.php', 
            'Dataface_FormTool_mobile_lookup'
        );
        
        // Registra azione AJAX per ricerca record
        $app->registerEventListener('afterAddExistingRelatedRecord', array($this, 'loadAssets'));
        
    }
    
    /**
     * Carica asset CSS e JS necessari
     */
    public function loadAssets() {
        $jt = Dataface_JavascriptTool::getInstance();
        $ct = Dataface_CSSTool::getInstance();
        
        // Carica Select2 da CDN (versione 4.1.0-rc.0 con miglior supporto mobile)
        $jt->addPath('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'select2');
        $ct->addPath('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', 'select2-css');
        
        // Carica CSS personalizzato per mobile
        $ct->addPath($this->getBaseURL() . '/css/mobile-lookup.css', 'mobile-lookup-css');
        
        // Carica JS del widget
        $jt->addPath($this->getBaseURL() . '/js/mobile-lookup.js', 'mobile-lookup-js');
    }
    
    /**
     * Ritorna base URL del modulo
     */
    public function getBaseURL() {
        if (!isset($this->baseURL)) {
            $this->baseURL = Dataface_ModuleTool::getInstance()->getModuleURL(__FILE__);
        }
        return $this->baseURL;
    }
    
}
