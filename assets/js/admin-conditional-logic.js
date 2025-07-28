jQuery(document).ready(function($) {
    console.log('PACL admin JS loaded');
    function injectConditionalLogic($row) {
        // Avoid duplicate injection
        if ($row.find('.pacl-conditional-logic').length) return;
        var fieldId = $row.find('[name^="product_addon["]').first().attr('name') || $row.find('input,select,textarea').first().attr('name');
        if (!fieldId) return;
        var $nonOptionRows = $row.find('.wc-pao-addon-content-non-option-rows').first();
        if (!$nonOptionRows.length) {
            // Create the section if missing
            var $addonContent = $row.find('.wc-pao-addon-content').first();
            $nonOptionRows = $('<div class="wc-pao-addon-content-non-option-rows"/>');
            $addonContent.append($nonOptionRows);
        }
        $nonOptionRows.removeClass('hide');
        $nonOptionRows.find('.pacl-conditional-logic').remove();
        var html = '';
        html += '<div class="wc-pao-row pacl-conditional-logic">';
        html += '<label style="min-width:140px;display:inline-block;font-weight:bold;">Conditional Logic</label>';
        html += '<span class="input-wrap">';
        html += '<label style="margin-right:8px;">Type</label>';
        html += '<select name="pacl_conditions['+fieldId+'][type]" style="width:auto;display:inline-block;margin-right:12px;">';
        html += '<option value="">None</option>';
        html += '<option value="show">Show if</option>';
        html += '<option value="hide">Hide if</option>';
        html += '</select>';
        html += '<label style="margin-right:8px;">Trigger Field</label>';
        html += '<input type="text" name="pacl_conditions['+fieldId+'][trigger]" style="width:120px;display:inline-block;margin-right:12px;" placeholder="field_id" />';
        html += '<label style="margin-right:8px;">Trigger Value</label>';
        html += '<input type="text" name="pacl_conditions['+fieldId+'][value]" style="width:120px;display:inline-block;" placeholder="value" />';
        html += '<span class="description" style="display:block;margin-top:4px;">Set logic: e.g. Show if [Trigger Field] equals [Trigger Value].</span>';
        html += '</span>';
        html += '</div>';
        $nonOptionRows.append(html);
        // Force visibility: remove 'hide' and set display:block on all parents
        var $logic = $nonOptionRows.find('.pacl-conditional-logic');
        $logic.parents().each(function(){
            $(this).removeClass('hide').css('display','block');
        });
    }

    function addConditionalLogicFields() {
        $('.wc-pao-addon').each(function() {
            injectConditionalLogic($(this));
        });
    }

    // Initial and on dynamic add-on add
    addConditionalLogicFields();
    $(document).on('click', '.wc-pao-add-addon, .wc-pao-add-option, .wc-pao-remove-addon', function() {
        setTimeout(addConditionalLogicFields, 300);
    });

    // MutationObserver to handle dynamic DOM changes
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            $(mutation.addedNodes).each(function() {
                var $node = $(this);
                if ($node.hasClass('wc-pao-addon')) {
                    injectConditionalLogic($node);
                } else if ($node.find('.wc-pao-addon').length) {
                    $node.find('.wc-pao-addon').each(function() {
                        injectConditionalLogic($(this));
                    });
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
});
