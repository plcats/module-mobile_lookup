/**
 * Mobile Lookup Widget - Javascript
 * 
 * Trasforma select standard in Select2 mobile-friendly
 * con ricerca AJAX e interfaccia touch-optimized
 * 
 * @author Paolo Bonzini
 * @version 1.1.0
 */

(function () {

    var $ = jQuery;
    var select2Loaded = false;
    var select2Loading = false;
    var select2LoadQueue = [];
    var SELECT2_CSS_ID = 'xf-mobile-lookup-select2-css';
    var SELECT2_JS_ID = 'xf-mobile-lookup-select2-js';

    function safeString(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value);
    }

    function escapeHtml(value) {
        return safeString(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function parseJsonOptions(raw, fallback) {
        if (!raw) {
            return fallback || {};
        }
        try {
            return JSON.parse(raw);
        } catch (e) {
            return fallback || {};
        }
    }

    /**
     * Porta il focus sulla casella di ricerca Select2 appena aperta.
     */
    function focusLookupSearchField($dropdown) {
        var $searchField = null;

        if ($dropdown && $dropdown.length) {
            $searchField = $dropdown.find('.select2-search__field').first();
        }
        if (!$searchField || !$searchField.length) {
            $searchField = $('.select2-container--open .select2-search__field').last();
        }
        if (!$searchField || !$searchField.length) {
            return;
        }

        var searchEl = $searchField.get(0);
        if (!searchEl) {
            return;
        }

        try {
            searchEl.focus({ preventScroll: true });
        } catch (e) {
            searchEl.focus();
        }

        if (searchEl.setSelectionRange) {
            var len = searchEl.value ? searchEl.value.length : 0;
            try {
                searchEl.setSelectionRange(len, len);
            } catch (err) { }
        }
    }

    function flushSelect2Queue() {
        var queue = select2LoadQueue.slice(0);
        select2LoadQueue = [];
        for (var i = 0; i < queue.length; i++) {
            try {
                queue[i]();
            } catch (e) { }
        }
    }

    /**
     * Carica Select2 da CDN dinamicamente
     */
    function loadSelect2(callback) {
        if (select2Loaded || (window.jQuery && $.fn.select2)) {
            select2Loaded = true;
            if (callback) callback();
            return;
        }

        if (callback) {
            select2LoadQueue.push(callback);
        }
        if (select2Loading) {
            return;
        }
        select2Loading = true;

        // Carica CSS
        if (!document.getElementById(SELECT2_CSS_ID)) {
            var cssLink = document.createElement('link');
            cssLink.id = SELECT2_CSS_ID;
            cssLink.rel = 'stylesheet';
            cssLink.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
            document.head.appendChild(cssLink);
        }

        function markLoadedAndFlush() {
            select2Loading = false;
            select2Loaded = !!(window.jQuery && $.fn.select2);
            flushSelect2Queue();
        }

        // Carica JS
        var existingScript = document.getElementById(SELECT2_JS_ID);
        if (!existingScript) {
            var script = document.createElement('script');
            script.id = SELECT2_JS_ID;
            script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            script.onload = markLoadedAndFlush;
            script.onerror = markLoadedAndFlush;
            document.head.appendChild(script);
        } else {
            existingScript.addEventListener('load', markLoadedAndFlush, { once: true });
            existingScript.addEventListener('error', markLoadedAndFlush, { once: true });
        }
    }

    function initMobileLookup(context) {
        // Prima carica Select2
        loadSelect2(function () {
            $('.xf-mobile-lookup', context).each(function () {
                var $select = $(this);
                var hasSelect2Instance = !!$select.data('select2');

                if (!hasSelect2Instance && $select.hasClass('select2-hidden-accessible')) {
                    $select.removeClass('select2-hidden-accessible')
                        .removeAttr('data-select2-id')
                        .removeAttr('tabindex')
                        .removeAttr('aria-hidden');
                    $select.siblings('.select2').remove();
                }

                // Evita doppia inizializzazione
                if ($select.data('mobile-lookup-initialized') && hasSelect2Instance) {
                    return;
                }
                if ($select.data('mobile-lookup-initialized') && !hasSelect2Instance) {
                    $select.removeData('mobile-lookup-initialized');
                }

                // Leggi opzioni da data attribute
                var options = parseJsonOptions($select.attr('data-mobile-lookup-options'), {});

                // Configurazione Select2 ottimizzata per mobile
                var useAllScreen = (options.allscreen === true || options.allscreen === 1 || options.allscreen === '1');
                var $allScreenOverlay = null;
                var $allScreenPanel = null;
                var openGuardTimer = null;
                var allScreenState = {
                    mounted: false,
                    containerParent: null,
                    containerNext: null,
                    $container: null
                };

                if (useAllScreen) {
                    $allScreenOverlay = $(
                        '<div class="xf-ml-allscreen" style="display:none;">' +
                        '<div class="xf-ml-allscreen-backdrop"></div>' +
                        '<div class="xf-ml-allscreen-panel">' +
                        '<div class="xf-ml-allscreen-header">' +
                        '<span class="xf-ml-allscreen-title">Seleziona</span>' +
                        '<button type="button" class="xf-ml-allscreen-close" aria-label="Chiudi">×</button>' +
                        '</div>' +
                        '<div class="xf-ml-allscreen-body"><div class="xf-ml-allscreen-field"></div></div>' +
                        '</div>' +
                        '</div>'
                    );
                    $('body').append($allScreenOverlay);
                    $allScreenPanel = $allScreenOverlay.find('.xf-ml-allscreen-field');

                    $allScreenOverlay.on('click', '.xf-ml-allscreen-backdrop, .xf-ml-allscreen-close', function () {
                        $select.select2('close');
                    });
                    $allScreenOverlay.on('mousedown', '.xf-ml-allscreen-close', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                }

                function mountAllScreen() {
                    if (!useAllScreen || !$allScreenOverlay) return;
                    if (!allScreenState.$container) {
                        allScreenState.$container = $select.next('.select2');
                    }
                    if (!allScreenState.mounted) {
                        if (allScreenState.$container && allScreenState.$container.length) {
                            allScreenState.containerParent = allScreenState.$container.parent();
                            allScreenState.containerNext = allScreenState.$container.next();
                        }

                        if (allScreenState.$container && allScreenState.$container.length) {
                            allScreenState.$container.detach().appendTo($allScreenPanel);
                        }
                        allScreenState.mounted = true;
                    }

                    $allScreenOverlay.show();
                    $('body').addClass('xf-ml-allscreen-open');
                }

                function unmountAllScreen() {
                    if (!useAllScreen || !$allScreenOverlay) return;
                    if (allScreenState.mounted) {
                        if (allScreenState.$container && allScreenState.$container.length) {
                            if (allScreenState.containerNext && allScreenState.containerNext.length) {
                                allScreenState.$container.insertBefore(allScreenState.containerNext);
                            } else if (allScreenState.containerParent && allScreenState.containerParent.length) {
                                allScreenState.$container.appendTo(allScreenState.containerParent);
                            }
                        }

                        allScreenState.mounted = false;
                    }

                    $allScreenOverlay.hide();
                    $('body').removeClass('xf-ml-allscreen-open');
                }

                var select2Config = {
                    // UI mobile-friendly
                    width: '100%',
                    dropdownAutoWidth: true,
                    placeholder: options.placeholder || 'Seleziona...',
                    allowClear: true,

                    // Ricerca
                    minimumInputLength: 0,

                    // Template personalizzati per mobile
                    templateResult: formatResult,
                    templateSelection: formatSelection,

                    // Dropdown positioning per mobile
                    dropdownCssClass: 'xf-mobile-lookup-dropdown',

                    // AJAX se configurato
                    ajax: options.table ? {
                        url: DATAFACE_SITE_HREF,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            function resolveDynamicFieldValue(fieldName) {
                                var $form = $select.closest('form');
                                var $row = $select.closest('tr');

                                // 1) Campo con nome esatto
                                var $exact = $('input[name="' + fieldName + '"], select[name="' + fieldName + '"]', $form);
                                if ($exact.length > 0) {
                                    return $exact.first().val();
                                }

                                // 2) Campo nella stessa riga tramite data-xf-field
                                if ($row.length > 0) {
                                    var $rowField = $row.find('[data-xf-field="' + fieldName + '"]');
                                    if ($rowField.length > 0) {
                                        return $rowField.first().val();
                                    }

                                    // 3) Fallback: name che termina con [fieldName]
                                    var suffix = '[' + fieldName + ']';
                                    var $suffixField = $row.find('input[name], select[name]').filter(function () {
                                        var n = $(this).attr('name') || '';
                                        return n.slice(-suffix.length) === suffix;
                                    });
                                    if ($suffixField.length > 0) {
                                        return $suffixField.first().val();
                                    }
                                }

                                return '';
                            }

                            // Risolvi filtri dinamici ($campo) con valori dal form
                            var resolvedFilters = {};
                            if (options.filters) {
                                for (var filterKey in options.filters) {
                                    var filterValue = options.filters[filterKey];
                                    var isNegated = false;

                                    // Controlla se inizia con ! (diverso da)
                                    if (typeof filterValue === 'string' && filterValue.charAt(0) === '!') {
                                        isNegated = true;
                                        filterValue = filterValue.substring(1); // Rimuovi !
                                    }

                                    // Se inizia con $, è riferimento a campo form
                                    if (typeof filterValue === 'string' && filterValue.charAt(0) === '$') {
                                        var fieldName = filterValue.substring(1);
                                        filterValue = resolveDynamicFieldValue(fieldName);
                                    }

                                    // Ricostruisci con prefisso ! se era negato
                                    if (isNegated) {
                                        filterValue = '!' + filterValue;
                                    }

                                    resolvedFilters[filterKey] = filterValue;
                                }
                            }

                            var payload = {
                                '-action': 'mobile_lookup_search',
                                '-table': options.table,
                                '-key': options.keycol || 'id',
                                '-label': options.labelcol || '',
                                '-image': options.image || '',
                                '-search': params.term || '',
                                '-searchFields': options.searchFields || options.labelcol || '',
                                '-filters': JSON.stringify(resolvedFilters),
                                '-page': params.page || 1
                            };

                            // Anchor: al primo open senza ricerca, centra sul selezionato
                            var anchorOpen = $select.data('ml-anchor-open');
                            var currentVal = $select.val();
                            if (anchorOpen && !params.term && currentVal) {
                                payload['-anchor'] = 1;
                                payload['-selected'] = currentVal;
                                $select.data('ml-anchor-open', false);
                            }

                            return payload;
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            var results = (data.results || []).map(function (item) {
                                if (!item.text || item.text === '') {
                                    if (item.label && item.label !== '') {
                                        item.text = item.label;
                                    } else if (item.id !== undefined && item.id !== null) {
                                        item.text = String(item.id);
                                    }
                                }
                                return item;
                            });
                            return {
                                results: results,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    } : undefined,

                    // Comportamento mobile
                    closeOnSelect: true,
                    selectOnClose: false,

                    // Lingua italiana
                    language: {
                        noResults: function () {
                            return "Nessun risultato trovato";
                        },
                        searching: function () {
                            return "Ricerca in corso...";
                        },
                        inputTooShort: function () {
                            return "Inserisci almeno " + (options.minimumInputLength || 0) + " caratteri";
                        },
                        loadingMore: function () {
                            return "Caricamento risultati...";
                        },
                        errorLoading: function () {
                            return "Errore nel caricamento";
                        }
                    }
                };

                if (useAllScreen && $allScreenPanel) {
                    select2Config.dropdownParent = $allScreenPanel;
                }

                var initialValue = $select.val();
                if ((!initialValue || initialValue === '') && $select.attr('value')) {
                    initialValue = $select.attr('value');
                }
                if (initialValue && initialValue !== '') {
                    var $initialOption = $select.find('option[value="' + initialValue + '"]');
                    if (!$initialOption.length) {
                        // Label provvisoria = chiave; verra' eventualmente aggiornata via AJAX.
                        $select.append(new Option(String(initialValue), initialValue, true, true));
                    } else {
                        $select.val(initialValue);
                    }
                }

                // Inizializza Select2
                $select.select2(select2Config);

                var $select2Container = $select.next('.select2');

                // Marca come inizializzato
                $select.data('mobile-lookup-initialized', true);

                // Allinea la label reale del valore iniziale (senza triggerare change).
                (function refreshInitialLabel() {
                    if (!initialValue || initialValue === '') {
                        return;
                    }

                    function applyInitialLabel(label) {
                        var finalLabel = (label && label !== '') ? label : String(initialValue);
                        var $opt = $select.find('option[value="' + initialValue + '"]');
                        if ($opt.length) {
                            $opt.text(finalLabel);
                        }

                        // Aggiorna direttamente il testo renderizzato da Select2.
                        var $rendered = $select.next('.select2').find('.select2-selection__rendered');
                        if ($rendered.length) {
                            $rendered.text(finalLabel).attr('title', finalLabel);
                            $rendered.removeClass('select2-selection__placeholder');
                        }
                    }

                    if (options.table && options.keycol) {
                        var filterParam = {};
                        filterParam[options.keycol] = initialValue;
                        $.ajax({
                            url: DATAFACE_SITE_HREF,
                            data: {
                                '-action': 'mobile_lookup_search',
                                '-table': options.table,
                                '-key': options.keycol,
                                '-label': options.labelcol || '',
                                '-searchFields': options.keycol,
                                '-search': '',
                                '-filters': JSON.stringify(filterParam),
                                '-page': 1
                            },
                            dataType: 'json',
                            success: function (d) {
                                var lbl = String(initialValue);
                                if (d && d.results && d.results.length > 0) {
                                    lbl = d.results[0].text || d.results[0].label || String(initialValue);
                                }
                                applyInitialLabel(lbl);
                            },
                            error: function () {
                                applyInitialLabel(String(initialValue));
                            }
                        });
                    } else {
                        applyInitialLabel(String(initialValue));
                    }
                })();

                // Gestione pulsante modifica voce selezionata
                var $wrapper = $select.closest('.xf-mobile-lookup-wrapper');
                var fieldName = safeString($select.attr('name'));
                var hasIndexedName = /\[[0-9]+\]/.test(fieldName);
                var isInsideRelationship = (
                    $select.closest('.xataface-RelatedList, .xataface-relationship, .xf-related-records').length > 0
                );
                var isGridContext = hasIndexedName || isInsideRelationship;
                if ($wrapper.length) {
                    $wrapper
                        .toggleClass('xf-mobile-lookup-in-grid', isGridContext)
                        .toggleClass('xf-mobile-lookup-standalone', !isGridContext);
                }
                var $editBtn = $wrapper.find('.xf-mobile-lookup-edit');
                var $newBtn = $wrapper.find('.xf-mobile-lookup-new');

                var maxWidthValue = options.maxWidth;
                if ((!maxWidthValue || maxWidthValue === '') && options.atts && options.atts.maxWidth) {
                    maxWidthValue = options.atts.maxWidth;
                }
                if (maxWidthValue) {
                    $wrapper.css('max-width', String(maxWidthValue));
                }
                var minWidthValue = options.minWidth;
                if ((!minWidthValue || minWidthValue === '') && options.atts && options.atts.minWidth) {
                    minWidthValue = options.atts.minWidth;
                }
                if (minWidthValue) {
                    $wrapper.css('min-width', String(minWidthValue));
                }
                function buildRecordId(value) {
                    if (!options.table || !value) return null;
                    var keycol = options.keycol || 'id';
                    return options.table + '?' + keycol + '=' + value;
                }
                function buildEditUrl(value) {
                    var recordId = buildRecordId(value);
                    if (!recordId) return null;
                    return DATAFACE_SITE_HREF + '?-table=' + encodeURIComponent(options.table)
                        + '&-action=edit&-recordid=' + encodeURIComponent(recordId);
                }
                function updateEditButton() {
                    var val = $select.val();
                    var url = buildEditUrl(val);
                    if (options.canEdit && url) {
                        $editBtn.attr('href', url).removeClass('is-disabled');
                    } else {
                        $editBtn.attr('href', '#').addClass('is-disabled');
                    }
                }
                function updateNewButton() {
                    if (options.canNew) {
                        $newBtn.attr('href', '#').removeClass('is-disabled');
                    } else {
                        $newBtn.attr('href', '#').addClass('is-disabled');
                    }
                }
                function openDialog(url, title) {
                    if (!url) return;

                    if (!$.ui || !$.ui.dialog) {
                        window.location.href = url;
                        return;
                    }

                    var dialogUrl = url;
                    dialogUrl += (dialogUrl.indexOf('?') === -1 ? '?' : '&') + '--hide-header=1&--hide-footer=1&--template=Dataface_Form_Template.html';

                    var $dlg = $('<div class="xf-mobile-lookup-dialog"></div>');
                    var $iframe = $('<iframe>', {
                        src: dialogUrl,
                        class: 'xf-RecordDialog-iframe',
                        scrolling: 'yes',
                        css: { width: '100%', height: '99%', border: 'none' }
                    });

                    $iframe.on('load', function () {
                        var iframe = $(this).contents();
                        if (!iframe) return;

                        try {
                            var iframeBodyText = $.trim(iframe.text() || '');
                            var jsonStart = iframeBodyText.indexOf('{');
                            if (jsonStart >= 0) {
                                var maybeJson = iframeBodyText.substring(jsonStart);
                                var parsed = JSON.parse(maybeJson);
                                if (parsed && String(parsed.response_code) === '200') {
                                    $dlg.dialog('close');
                                    return;
                                }
                            }
                        } catch (err) { }

                        var portalMessage = iframe.find('.portalMessage');
                        portalMessage.detach();

                        iframe.find('.xf-button-bar').remove();

                        var dc = iframe.find('.documentContent').first();
                        if (dc.length === 0) dc = iframe.find('#main_section');
                        if (dc.length === 0) dc = iframe.find('#main_column');

                        if (dc.length > 0) {
                            dc.remove();
                            dc.prepend(portalMessage);

                            var ibody = iframe.find('body');
                            var hidden = $(':hidden', ibody);

                            ibody.addClass('RecordDialogBody').empty();
                            $('script', dc).remove();
                            dc.appendTo(ibody);

                            hidden.each(function () {
                                if (this.tagName === 'SCRIPT') return;
                                $('script', this).remove();
                                $(this).appendTo(ibody);
                                $(this).hide();
                            });

                            $('#details-controller, .contentViews, .contentActions, .insert-record-label, .edit-record-label', ibody).hide();
                            ibody.css('background-color', 'transparent');
                            $('.documentContent', ibody).css({
                                'border': 'none',
                                'margin': 0,
                                'padding': 0,
                                'background-color': 'transparent',
                                'overflow': 'auto'
                            });
                        }
                    });

                    $dlg.append($iframe);

                    var w = Math.min(window.innerWidth * 0.9, 1100);
                    var h = Math.min(window.innerHeight * 0.9, 800);

                    $dlg.dialog({
                        modal: true,
                        title: title,
                        width: w,
                        height: h,
                        resizable: true,
                        close: function () {
                            $dlg.dialog('destroy').remove();
                        }
                    });
                }

                function openEditDialog(url) {
                    openDialog(url, 'Modifica voce selezionata');
                }

                if ($editBtn.length) {
                    $editBtn.on('click', function (e) {
                        if ($(this).hasClass('is-disabled')) {
                            e.preventDefault();
                            return;
                        }
                        e.preventDefault();
                        var url = $(this).attr('href');
                        openEditDialog(url);
                    });
                }

                function openNewDialog() {
                    var url = DATAFACE_SITE_HREF + '?-table=' + encodeURIComponent(options.table) + '&-action=new';
                    openDialog(url, 'Nuova voce');
                }

                if ($newBtn.length) {
                    $newBtn.on('click', function (e) {
                        e.preventDefault();
                        if ($(this).hasClass('is-disabled')) {
                            return;
                        }
                        openNewDialog();
                    });
                }

                // Aggiorna pulsante modifica dopo init e ad ogni cambio
                updateEditButton();
                $select.off('change.mobileLookupEdit').on('change.mobileLookupEdit', updateEditButton);
                updateNewButton();

                var openOnReleasePending = false;
                var blockOpeningUntilRelease = false;
                var programmaticOpenInProgress = false;

                var wrapperEl = $wrapper.get(0);
                if (wrapperEl) {
                    var releaseCaptureStart = function (ev) {
                        if (!useAllScreen) return;

                        var target = ev.target;
                        if (!target || !wrapperEl.contains(target)) return;

                        var $target = $(target);
                        if (!$target.closest('.select2-selection').length) return;
                        if ($target.closest('.select2-selection__clear').length) return;

                        openOnReleasePending = true;
                        blockOpeningUntilRelease = true;

                        ev.preventDefault();
                        ev.stopPropagation();

                        $(document)
                            .off('mouseup.mobileLookupRelease touchend.mobileLookupRelease touchcancel.mobileLookupRelease')
                            .one('mouseup.mobileLookupRelease touchend.mobileLookupRelease touchcancel.mobileLookupRelease', function () {
                                if (!openOnReleasePending) return;
                                openOnReleasePending = false;
                                blockOpeningUntilRelease = false;

                                programmaticOpenInProgress = true;
                                $select.select2('open');
                                setTimeout(function () {
                                    programmaticOpenInProgress = false;
                                }, 0);
                            });
                    };

                    wrapperEl.addEventListener('mousedown', releaseCaptureStart, true);
                    wrapperEl.addEventListener('touchstart', releaseCaptureStart, true);
                }

                // Mobile: aumenta dimensione dropdown per touch
                $select.off('select2:opening.mobileLookup').on('select2:opening.mobileLookup', function (e) {
                    if (useAllScreen && blockOpeningUntilRelease && !programmaticOpenInProgress) {
                        e.preventDefault();
                        return false;
                    }
                });

                $select.off('select2:open.mobileLookup').on('select2:open.mobileLookup', function () {
                    $select.data('ml-anchor-open', true);
                    $select.data('ml-just-opened', true);
                    if (openGuardTimer) {
                        clearTimeout(openGuardTimer);
                    }
                    openGuardTimer = setTimeout(function () {
                        $select.data('ml-just-opened', false);
                    }, 120);

                    // Mount allscreen al rilascio click (tick successivo) per
                    // evitare che il click di apertura venga interpretato come select.
                    setTimeout(function () {
                        if (useAllScreen && $allScreenOverlay) {
                            mountAllScreen();
                        }

                        var $dropdown = $('.select2-container--open .select2-dropdown').last();
                        if (!$dropdown.length) {
                            return;
                        }

                        $dropdown.find('.select2-results__option').css({
                            'padding': '12px 16px',
                            'font-size': '16px' // Previene zoom su iOS
                        });

                        focusLookupSearchField($dropdown);

                        if (useAllScreen && $allScreenOverlay) {
                            requestAnimationFrame(function () {
                                var $panel = $allScreenOverlay.find('.xf-ml-allscreen-panel');
                                var $header = $allScreenOverlay.find('.xf-ml-allscreen-header');
                                var $body = $allScreenOverlay.find('.xf-ml-allscreen-body');
                                var $field = $allScreenOverlay.find('.xf-ml-allscreen-field');
                                var $search = $dropdown.find('.select2-search--dropdown');
                                var panelH = $panel.innerHeight() || $panel.outerHeight() || 0;
                                var headerH = $header.outerHeight(true) || 0;
                                var bodyPadding = (($body.outerHeight() || 0) - ($body.height() || 0));
                                var searchH = $search.outerHeight(true) || 0;
                                var reservedSpace = headerH + bodyPadding + searchH + 24;
                                // Compensa l'offset CSS negativo (es. top:-28px) e
                                // aggiunge un piccolo extra per eliminare il gap in fondo.
                                var cssTop = parseInt($dropdown.css('top'), 10);
                                if (isNaN(cssTop)) cssTop = 0;
                                var topOffsetCompensation = cssTop < 0 ? Math.abs(cssTop) : 0;
                                var bottomGapExtra = 16;
                                var dropdownH = Math.max(180, panelH - reservedSpace + topOffsetCompensation);
                                dropdownH += bottomGapExtra;
                                var resultsH = Math.max(120, dropdownH - searchH - 8);

                                if (dropdownH > 0) {
                                    $dropdown.css('height', dropdownH + 'px');
                                }
                                if (resultsH > 0) {
                                    $field.css('min-height', resultsH + searchH + 'px');
                                    $dropdown.find('.select2-results').css('height', resultsH + 'px');
                                    $dropdown.find('.select2-results__options').css('height', resultsH + 'px');
                                }

                                focusLookupSearchField($dropdown);
                            });
                        }
                    }, 0);
                });

                $select.off('select2:close.mobileLookup').on('select2:close.mobileLookup', function () {
                    if (openGuardTimer) {
                        clearTimeout(openGuardTimer);
                        openGuardTimer = null;
                    }
                    openOnReleasePending = false;
                    blockOpeningUntilRelease = false;
                    $(document).off('mouseup.mobileLookupRelease touchend.mobileLookupRelease touchcancel.mobileLookupRelease');
                    $select.data('ml-just-opened', false);
                    unmountAllScreen();
                });

                $select.off('select2:selecting.mobileLookup').on('select2:selecting.mobileLookup', function (e) {
                    if (useAllScreen && $select.data('ml-just-opened')) {
                        e.preventDefault();
                        return false;
                    }
                });

                $select.off('select2:select.mobileLookup').on('select2:select.mobileLookup', function (e) {
                    if (useAllScreen && $allScreenOverlay) {
                        $select.select2('close');
                    }

                    // Forza il testo dell'<option> alla label dall'AJAX result
                    // Risolve il bug per cui Select2 mostra la chiave invece della label
                    var data = e.params && e.params.data;
                    if (data && data.id !== undefined && data.text) {
                        var $opt = $select.find('option[value="' + data.id + '"]');
                        if ($opt.length && $opt.text() !== data.text) {
                            $opt.text(data.text);
                        }
                    }

                    triggerAjaxValueUpdates($select);
                });

                /**
                 * Cerca nel form tutti i campi che hanno data-xf-update-url
                 * contenente {fieldName} come token, risolve l'URL e popola
                 * il campo target se le condizioni sono soddisfatte.
                 */
                function triggerAjaxValueUpdates($sourceSelect) {
                    var fieldName = $sourceSelect.attr('name');
                    if (!fieldName) return;
                    var newValue = $sourceSelect.val();
                    var $form = $sourceSelect.closest('form');
                    if (!$form.length) return;

                    // Cerca tutti gli input/select del form con data-xf-update-url
                    $form.find('[data-xf-update-url]').each(function () {
                        var $target = $(this);
                        var urlTemplate = $target.attr('data-xf-update-url');

                        // Controlla se il template contiene {fieldName}
                        var token = '{' + fieldName + '}';
                        if (!urlTemplate || urlTemplate.indexOf(token) === -1) return;

                        // Rispetta data-xf-update-condition=empty
                        var condition = $target.attr('data-xf-update-condition');
                        if (condition === 'empty') {
                            var currentVal = $target.val();
                            if (currentVal && currentVal !== '') return; // campo già compilato
                        }

                        // Risolvi il token con il nuovo valore
                        var resolvedUrl = urlTemplate.replace(new RegExp(token.replace(/[{}]/g, '\\$&'), 'g'), encodeURIComponent(newValue));

                        // Estrai il path fragment JSON (es. #0.Nazione)
                        var fragmentPath = null;
                        var hashIdx = resolvedUrl.indexOf('#');
                        if (hashIdx !== -1) {
                            fragmentPath = resolvedUrl.substring(hashIdx + 1);
                            resolvedUrl = resolvedUrl.substring(0, hashIdx);
                        }

                        // Fetch AJAX
                        $.ajax({
                            url: resolvedUrl,
                            dataType: 'json',
                            success: function (data) {
                                var result = data;

                                // Naviga nel path JSON (es. "0.Nazione")
                                if (fragmentPath) {
                                    var parts = fragmentPath.split('.');
                                    for (var i = 0; i < parts.length; i++) {
                                        if (result === null || result === undefined) break;
                                        result = result[parts[i]];
                                    }
                                }

                                if (result === null || result === undefined || result === '') return;

                                // Scrivi il valore nel campo target
                                // Se è un mobile_lookup (Select2), recupera la label dal
                                // lookup del target e crea l'option con il testo corretto
                                if ($target.hasClass('xf-mobile-lookup') && $target.data('select2')) {
                                    var targetOpts = parseJsonOptions($target.attr('data-mobile-lookup-options'), {});

                                    function setMobileLookupValue(label) {
                                        var $existOpt = $target.find('option[value="' + result + '"]');
                                        if ($existOpt.length) {
                                            $existOpt.text(label);
                                        } else {
                                            $target.append(new Option(label, result, true, true));
                                        }
                                        $target.val(result).trigger('change.select2');
                                    }

                                    // Se abbiamo table+keycol+labelcol, recupera la label via AJAX
                                    if (targetOpts.table && targetOpts.keycol && targetOpts.labelcol) {
                                        var filterParam = {};
                                        filterParam[targetOpts.keycol] = result;
                                        $.ajax({
                                            url: DATAFACE_SITE_HREF,
                                            data: {
                                                '-action': 'mobile_lookup_search',
                                                '-table': targetOpts.table,
                                                '-key': targetOpts.keycol,
                                                '-label': targetOpts.labelcol,
                                                '-searchFields': targetOpts.keycol,
                                                '-search': '',
                                                '-filters': JSON.stringify(filterParam),
                                                '-page': 1
                                            },
                                            dataType: 'json',
                                            success: function (d) {
                                                var lbl = result; // fallback alla chiave
                                                if (d && d.results && d.results.length > 0) {
                                                    lbl = d.results[0].text || d.results[0].label || result;
                                                }
                                                setMobileLookupValue(lbl);
                                            },
                                            error: function () {
                                                setMobileLookupValue(result); // fallback
                                            }
                                        });
                                    } else {
                                        setMobileLookupValue(result);
                                    }
                                } else {
                                    $target.val(result).trigger('change');
                                }
                            }
                        });
                    });
                }

                // Callback custom se configurato
                if (options.callback && typeof options.callback === 'function') {
                    $select.off('select2:select.mobileLookupCallback').on('select2:select.mobileLookupCallback', function (e) {
                        options.callback(e.params.data);
                    });
                }
            });
        }); // Fine loadSelect2 callback
    }

    /**
     * Formatta risultato nella dropdown
     */
    function formatResult(result) {
        if (result.loading) {
            return result.text || result.label || result.id || '';
        }

        var imageHtml = '';
        if (result.image) {
            imageHtml = '<div class="result-image"><img src="' + escapeHtml(result.image) + '" alt=""></div>';
        }

        var titleText = escapeHtml(result.text || result.label || result.id || '');
        var descriptionText = result.description ? '<div class="result-description">' + escapeHtml(result.description) + '</div>' : '';

        var $container = $(
            '<div class="mobile-lookup-result">' +
            imageHtml +
            '<div class="result-content">' +
            '<div class="result-title">' + titleText + '</div>' +
            descriptionText +
            '</div>' +
            '</div>'
        );

        return $container;
    }

    /**
     * Formatta selezione corrente
     */
    function formatSelection(selection) {
        // Priorità: text dall'AJAX result
        var txt = selection.text;
        // Fallback: testo dell'elemento <option> nel DOM
        if ((!txt || txt === String(selection.id)) && selection.element) {
            var elTxt = $(selection.element).text().trim();
            if (elTxt && elTxt !== String(selection.id)) {
                return elTxt;
            }
        }
        return txt || selection.label || selection.id || '';
    }

    /**
     * Detect mobile device
     */
    function isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
            window.innerWidth < 768;
    }

    /**
     * Registra inizializzatore Xataface
     */
    if (typeof registerXatafaceDecorator === 'function') {
        registerXatafaceDecorator(function (node) {
            initMobileLookup(node);
        });
    }

    /**
     * Inizializza al document ready
     */
    $(document).ready(function () {
        initMobileLookup(document);
    });

})();
