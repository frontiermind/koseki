var targetPutColor;

function popColorPicker(fObj) {
	targetPutColor = fObj;
	if (navigator.userAgent.indexOf("Opera",0) != -1) {
		//Opera
		colorPicker.style.left = window.event.clientX;
		colorPicker.style.top = window.event.clientY;
	} else if (document.all) {
		//IE
		colorPicker.style.left = document.body.scrollLeft + window.event.clientX;
		colorPicker.style.top = document.body.scrollTop + window.event.clientY;
	} else if (document.getElementById) {
		//Netscape 6
		colorPicker.style.left = window.pageXOfset + event.pageX;
		colorPicker.style.top = window.pageYOffset + event.pageY;
	} else if (document.layers) {
		//Netscape 4
		colorPicker.style.left = window.event.x;
		colorPicker.style.top = window.event.y;
	}
	colorPicker.style.visibility = 'visible';
	return;
}

function putColor(color) {
	targetPutColor.value = '#' + color;
	colorPicker.style.visibility = 'hidden';
	targetPutColor = null;
	return;
}

function closePicker() {
	if (colorPicker.style.visibility == 'visible') {
		colorPicker.style.visibility = 'hidden';
	}
}

function delFormAlert(type, url) {
	if (type == 1) {
		if (confirm('���̃t�H�[���ݒ���폜���Ă���낵���ł����H')) {
			location.href = url;
		}
	} else {
		if (confirm('�y���Ӂz\n�t�H�[���ݒ���폜����ƁA�񓚃f�[�^����������܂��B\n���炩���ߍŐV�̉񓚃f�[�^���_�E�����[�h���Ă��������B\n\n���̃t�H�[���ݒ���폜���Ă���낵���ł����H')) {
			location.href = url;
		}
	}
}

function delQueryAlert(url) {
	if (confirm('���̎��⍀�ڂ��폜���Ă���낵���ł����H')) {
		location.href = url;
	}
}