jQuery(function(){
	update_countdown();
	setInterval(update_countdown, 60000);
	
	if (jQuery('body').hasClass('logged-in'))
		jQuery('.login-link.f-right').text('My Account');
});

function update_countdown() {
	var now = new Date();
	if (now.getMonth() == 11) {
		var next = new Date(now.getFullYear() + 1, 0, 1);
	} else {
		var next = new Date(now.getFullYear(), now.getMonth() + 1, 1);
	}
	
	jQuery('.month_name').text(next.toLocaleString('default', { month: 'long' }));
	
	diffTime = Math.abs(next - now)/1000;
	diffDays = Math.ceil(diffTime / (60 * 60 * 24)) - 1; 
	diffHours = Math.ceil((diffTime - diffDays * 24 * 60 * 60) / 3600) - 1; 
	diffMins = Math.ceil((diffTime - diffDays * 24 * 60 * 60 - diffHours * 60 * 60) / 60) - 1;
	
	var el = jQuery('[data-id=8fd134b]');
	if (diffDays < 11) {
		el.show(100);
		el.find('.days .number').text(diffDays);
		el.find('.hours .number').text(diffHours);
		el.find('.mins .number').text(diffMins);
	} else {
		el.hide();
	}
}