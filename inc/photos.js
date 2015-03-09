// JavaScript Document

function set_stars(photo_star) {
	pos_x = photo_star.indexOf('x');
	photo_id = photo_star.substring(0, pos_x);
	star_id = photo_star.substring(pos_x + 1);

	for(i = 1; i <= star_id; i++) {
		document.getElementById('b' + photo_id + 'x' + i).style.zIndex = '4';
	}

	star_id++;	// use ++ instead of + 1 because ++ forces to convert to integer
	for(i = star_id; i <= 5; i++) {
		document.getElementById('w' + photo_id + 'x' + i).style.zIndex = '4';
	}
}

function reset_stars(photo_id) {
	for(i = 1; i <= 5; i++) {
		document.getElementById('b' + photo_id + 'x' + i).style.zIndex = '2';
		if(i > 1)
			document.getElementById('w' + photo_id + 'x' + i).style.zIndex = '1';
	}
}

function record_rating(photo_id, rate, my_div) {
	url = 'inc/ajax.php?content=rating&photo_id='+ photo_id +'&rate='+ rate +'&div='+ my_div;
	getData(url, my_div);
}

function delete_photo_gallery(photo_id, control_code) {
	if(confirm('¿Estás seguro que quieres borrar esta foto?')) {
		url = 'inc/ajax.php?content=delete_photo_gallery&photo_id='+ photo_id +'&code='+ control_code;
		getData(url, 'photo_detail_container');
	}
}

function go_to_view(view, detail) {
	url = 'index.php?mod=phtos&view='+ view;
	if(typeof detail != "undefined")
		url+= '&detail='+ detail;

}