/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    var time = new Date($('#delete-time').text()).getTime();

    var intervalId = window.setInterval(function () {
		var currentTime = new Date().getTime();
		var currentTime1 = Math.floor(currentTime + 71800);
        var difference = time - currentTime1;

        var days = Math.floor(difference / (1000 * 60 * 60 * 24));
        var hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((difference % (1000 * 60)) / 1000);
        
        var line = seconds + ' s.';
        if(minutes > 0)
            line = minutes + ' min. '+ line;
        if(hours > 0)
            line = hours + ' val. ' + line;
        if(days > 0)
            line = days + ' d. ' + line;
        
        
        $('#count').html(line);
		
        if(line === '0 s.'){
			window.clearInterval(intervalId);
			$('#count').html('<img src=images/loading3.gif>');
			window.location.href = 'index.php';
        }
    }, 1000);
	
	$('.copy').click(function(){
        $(this).closest('div').find('.input').select();
        document.execCommand('copy');
    });
    
});