$(function(){
  $('textarea').each(function(i,e){
    theTextarea = $(this);
    theTextarea.height((theTextarea[0].scrollHeight-5) +'px');
  });

  $('li').each(function(i,e){
    if(!$(this).hasClass('noCheckbox')){
      var uuid = 'li_' + Math.floor(Math.random() * Math.floor(1000000)).toString() + '_' + i.toString();
      $(this).contents().wrap('<span id="'+ uuid +'"><label for="cb_'+ uuid +'"></label></span>');
      $(this).prepend('<input type="checkbox" class="completeBox" id="cb_' + uuid +'" rel="'+ uuid +'" />')
    }
  });

  $('code,div.codeBlock,textarea.codeBlock').each(function(i,e){
    theElement = $(this);
    var lines = theElement.html().split("\n");
    theElement.empty();
    for(l=0;l<lines.length;l++){
      if($.trim(lines[l]) != '' && $.trim(lines[l]).substr(0,1) != '#' && $.trim(lines[l]).indexOf(' #') == -1 && lines[l].substr(0, 4).toUpperCase() != 'REM '){
        theElement.append('<input type="image" src="images/clipboard.png" value="" class="copy-text" rel="copy_'+ i +'_'+ l +'" data-clipboard-text="'+ $.trim(lines[l].replace(/"/g, '&quot;')) +'" /><span id="copy_'+ i +'_'+ l +'">'+ lines[l] +'</span>');
      } else {
        theElement.append(lines[l]);
      }
    }
  });

  $(document).on('click','input.copy-text',function(){
    theButton = $(this);
    $('input.copy-text').attr('src','images/clipboard.png');
    $('span.copy-animation,span.copy-animation-ps').removeClass('copy-animation copy-animation-ps');
    try {
      if($('#'+ theButton.attr('rel')).parent('div').hasClass('PS')){
        $('#'+ theButton.attr('rel')).addClass('copy-animation-ps');
      } else if($('#'+ theButton.attr('rel')).parent('div').hasClass('CMD')){
        $('#'+ theButton.attr('rel')).addClass('copy-animation-cmd');
      } else {
        $('#'+ theButton.attr('rel')).addClass('copy-animation');
      }
      navigator.clipboard.writeText(theButton.data('clipboard-text').replace(/<[^>]*>?/gm, ''));
      theButton.attr('src','images/clipboard_active.png');
    } catch(err) {
    }
    return false;
  });

  $(document).on('click','input.completeBox',function(){
    theBox = $(this);
    $('#'+ theBox.attr('rel')).addClass('strikethrough');
    theBox.prop('disabled',true);
    theBox.parent('li').prevAll().each(function(i,e){
      theLI = $(this);
      if(theLI.find('input[type=checkbox]').not(':checked')){
        $('#'+ theLI.find('input[type=checkbox]').attr('rel')).addClass('strikethrough');
        theLI.find('input[type=checkbox]').prop('checked',true).prop('disabled',true);
      }
    });
  });
});