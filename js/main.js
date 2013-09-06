$(document).ready(function() {
	var appId = 3863760,
		siteUrl = "http://glitch.loc";

	var button = $("#button");

	button.click(function() {
		button.animate({
			marginTop: "-=100",
			opacity: 0
		}, 500, function() {
			location.href = "https://oauth.vk.com/authorize?client_id="+ appId +"&scope=2&redirect_uri="+ siteUrl +"&response_type=code&v=5.0";
		});
	});
});