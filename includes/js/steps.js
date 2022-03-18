$(function(){
  stepCount = 0;
  console.log('%ci12bretro', 'font-weight: 900; font-size: 40px; color: #f00; text-shadow: 3px 3px 0 #000, 4px 4px 0 #fff; border-bottom: 8px double #f00; padding: 0 8px;');
  console.log('%chttps://youtube.com/c/i12bretro', 'font-size: 9px; margin-left: 60px;');
  stepsText = [];
  $('textarea').each(function(i,e){
    theTextarea = $(this);
    theTextarea.height((theTextarea[0].scrollHeight-5) +'px');
  });

  $('li').each(function(i,e){
    pi = 0;
    if(!$(this).hasClass('noCheckbox')){
      var uuid = `li_${(Math.floor(Math.random() * Math.floor(1000000))).toString()}_${i.toString()}`;
      $(this).contents().wrap(`<span id="${uuid}"><label for="cb_${uuid}"></label></span>`);
      $(this).prepend(`<input type="checkbox" class="completeBox" id="cb_${uuid}" rel="${uuid}" data-pi="${pi}" />`);
      pi++;
    }
    stepCount = pi;
  });

  $('code,div.codeBlock,textarea.codeBlock').each(function(i,e){
    theElement = $(this);
    var lines = theElement.html().split("\n");
    theElement.empty();
    for(l=0;l<lines.length;l++){
      if($.trim(lines[l]) != '' && $.trim(lines[l]).substr(0,1) != '#' && $.trim(lines[l]).indexOf(' #') == -1 && lines[l].substr(0, 4).toUpperCase() != 'REM '){
        stepsText.push($('<p/>').html($.trim(lines[l]).replace(/<br \/>$|<br\/>$|<br>$/g,'')).text());
        theElement.append(`<input type="image" src="images/clipboard.png" value="" class="copy-text" data-step-index="${(stepsText.length-1)}" rel="copy_${i}_${l}" /><span id="copy_${i}_${l}">${lines[l]}</span>`);
      } else {
        theElement.append(lines[l]);
      }
    }
  });

  $(document).on('click','input.copy-text',function(){
    theButton = $(this);
    $('input.copy-text').attr('src','images/clipboard.png');
    $('span.copy-animation,span.copy-animation-ps,span.copy-animation-cmd').removeClass('copy-animation copy-animation-ps copy-animation-cmd');
    try {
      if($('#'+ theButton.attr('rel')).parent('div').hasClass('PS')){
        $('#'+ theButton.attr('rel')).addClass('copy-animation-ps');
      } else if($('#'+ theButton.attr('rel')).parent('div').hasClass('CMD')){
        $('#'+ theButton.attr('rel')).addClass('copy-animation-cmd');
      } else {
        $('#'+ theButton.attr('rel')).addClass('copy-animation');
      }
      console.log('%cCopied:', 'font-style: italic; color: #0f0;');
      console.log(`%c${stepsText[theButton.data('step-index')]}`, 'margin-left: 25px;');
      navigator.clipboard.writeText(stepsText[theButton.data('step-index')]);
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

  $('#gridContainer').append('<div>&nbsp;</div><div id="footerLinks"></div>');
  $.ajax({ url: './includes/sites.json', type: 'get', dataType: 'json', success: function(r){
      $.each(r, function(k,v){
        $('#footerLinks').append(`<a href="${r[k].url}" target="_blank"><img src="${r[k].icon}" alt="${r[k].text}" title="${r[k].text}" /></a>`);
      });
    }
  });
});