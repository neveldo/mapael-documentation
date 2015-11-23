$(function(){
    $("a[href^='#']").on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $(this.hash).offset().top - 60}, 500);
    });

    SyntaxHighlighter.defaults['toolbar'] = false;
    SyntaxHighlighter.all();
});