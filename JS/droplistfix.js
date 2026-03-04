// This use to be keep drop list stay hover because Firefox back then don't keep it when drop list is active.
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown_control').forEach(function(select) {
        select.addEventListener('mousedown', function(e) {
            if (document.activeElement === select) {
                e.preventDefault();
                select.blur();
            }
        });
        select.addEventListener('change', function() {
            select.blur();
        });
    });
});