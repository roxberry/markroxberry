var pagecoll_requrl, pagecoll_nextquote, pagecoll_loading, pagecoll_errortext;

function pagesliders_init(requrl, nextquote, loading, errortext)
{
	pagecoll_requrl = requrl;
	pagecoll_nextquote = nextquote;
	pagecoll_loading = loading;
	pagecoll_errortext = errortext;

}

function pagesliders_refresh(numinstance,p1,p2,p3,p4,p5,p6,p7,p8,p9,p10,end)
{
	jQuery("#subpageslider_nextquote-"+numinstance).html(pagecoll_loading);
	jQuery.ajax({
		type: "POST",
		url: pagecoll_requrl,
		data: "refresh="+numinstance+"&page_num="+numinstance+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&p4="+p4+"&p5="+p5+"&p6="+p6+"&p7="+p7+"&p8="+p8+"&p9="+p9+"&p10="+p10+"&end="+end,
		success: function(response) {
			jQuery("#subpageslider_randomquote-"+numinstance).hide();
			jQuery("#subpageslider_randomquote-"+numinstance).html( response );
			jQuery("#subpageslider_randomquote-"+numinstance).fadeIn("slow");	
		},
		error: function(xhr, textStatus, errorThrown) {
			alert(textStatus+' '+xhr.status+': '+errorThrown);
			jQuery("#subpageslider_nextquote-"+numinstance).html('<a class=\"subpageslider_refresh\" style=\"cursor:pointer\" onclick=\"pagesliders_refresh('+numinstance+')\">'+pagecoll_nextquote+' &raquo;</a>');
		}	
	});
}