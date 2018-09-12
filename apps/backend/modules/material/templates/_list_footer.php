<script>
    $(function() {
        $(document).on('change', '.material-status', function(e) {
            var $element = $(e.target);

            $.post($element.data('url'), {
                material_id: $element.data('material-id'),
                material_status: $element.is(':checked') ? 1 : 0
            }, function() {

            });
        });
    });
</script>
