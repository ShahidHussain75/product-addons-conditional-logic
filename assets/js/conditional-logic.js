jQuery(document).ready(function($){
    // Load conditions from localized object or JSON script tag
    var conditions = window.pacl_conditions && window.pacl_conditions.conditions ? window.pacl_conditions.conditions : {};
    if ($.isEmptyObject(conditions)) {
        var jsonTag = document.getElementById('pacl-conditions-json');
        if (jsonTag) {
            try { conditions = JSON.parse(jsonTag.textContent); } catch(e) {}
        }
    }
    function checkConditions() {
        $.each(conditions, function(field_id, cond) {
            var triggerVal = $('[name="addon['+cond.trigger+']"]').val();
            var $fieldWrap = $('[name="addon['+field_id+']"]').closest('.wc-pao-addon-container, .form-row');
            if (cond.type === 'show') {
                if (triggerVal == cond.value) {
                    $fieldWrap.show();
                } else {
                    $fieldWrap.hide();
                }
            } else if (cond.type === 'hide') {
                if (triggerVal == cond.value) {
                    $fieldWrap.hide();
                } else {
                    $fieldWrap.show();
                }
            }
        });
    }
    // Initial check
    checkConditions();
    // Listen for changes
    $(document).on('change', '[name^="addon["]', function(){
        checkConditions();
    });
});
