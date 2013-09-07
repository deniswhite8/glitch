$(document).ready(function() {
	var appId = 3863760,
		siteUrl = "http://glitch.loc";

	var button = $("#button"),
		loading = $(".loading");

	button.click(function() {
		loading.css("visibility", "visible");

		loading.animate({
			marginTop: "-=200",
			opacity: 1
		}, 500);

		button.animate({
			marginTop: "-=200",
			opacity: 0
		}, 500, function() {

			location.href = "https://oauth.vk.com/authorize?client_id="+ appId +"&scope=2&redirect_uri="+ siteUrl +"&response_type=code&v=5.0";
		});
	});
});