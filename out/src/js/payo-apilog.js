payolution_jQuery(function($){
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    var xmlModal = $('#xmlModal');
    $('.showFull').bind('click', function(){
        xmlModal.html("<pre>" + escapeHtml($(this).data('full')) + "</pre>");
    });
});
