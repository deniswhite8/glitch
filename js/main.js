$(document).ready(function() {

	var appId = 3863760,
		siteUrl = "http://glitch.loc";

	var button = $("#button"),
		loading = $("#loading"),
		wrapper = $("#wrapper"),
		share = $("#share");

	function getWall(parent, conf) {
		conf.mas.forEach(function(i) {
			var img = $("<a target=\"_blank\" href=\"http://vk.com/id"+i.id+"\"><img src=\""+i.photo+"\">");
			parent.append(img);
		});
	}

	function wallPost(message, image, callback) {
		VK.Api.call('photos.getWallUploadServer', {v:"5.0"}, function (data) {
			if (data.response) {
				$.post('php/upload.php', {
					upload_url: data.response.upload_url,
					image: image
				}, function (json) {
					VK.Api.call("photos.saveWallPhoto", json, function (data) {
						VK.Api.call('wall.post', {
							message: message,
							attachments: [data.response[0].id]
							}, callback);
					});
				}, "json");
			}
		});
	}


	button.click(function() {
		VK.Auth.getLoginStatus(function(response) {
			if (response.session) {
				start(response);
			} else {
				VK.Auth.login(start, 6);
			}
		});
	});

	function start(response) {
		loading.css("visibility", "visible");

		loading.animate({
			marginTop: "-=200",
			opacity: 1
		}, 500);

		button.animate({
			marginTop: "-=200",
			opacity: 0
		}, 500, function() {button.hide();});

		VK.Api.call("friends.get", {v:"5.0", fields:["photo_max"], order:"random", count:3}, function(r) {
			if(r.response) {
				var obj = [];
				r.response.items.forEach(function(i) {
					obj.push({id:i.id, photo:i.photo_max});
				});

				var ok = false;
				var _obj;
				$.ajax({
					url: "php/glitch.php",
					data: {data:{id:response.session.mid, mas:obj}},
					type: "POST",
					complete: function() {
						loading.animate({
							marginTop: "-=200",
							opacity: 0
						}, 500, function() {
							loading.hide();
							if(ok) {
								wrapper.addClass("wall");
								var json = JSON.parse(_obj);
								getWall(wrapper, json);
								share.show();
								share.click(function() {
									wallPost("glitch em", json.wall, function() {
										share.text("share it");
										share.removeAttr("disabled");
										share.removeClass("dis");
									});
									share.text("please wait...");
									share.attr("disabled", "disabled");
									share.addClass("dis");
								});
							}
						});
					},
					error: function() {
						console.log("ajax error");
					},
					success: function(obj) {
						ok = true;
						_obj = obj;
					}
				});
			}
		});
	}
});
