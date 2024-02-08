var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
var modal = document.getElementById("modalimg");

function ver (id) {
  
	
	$.post(base_url + 'pedidos/get_img/' + id, function (data) {
        modal.style.display = "block";
        modalImg.src = base_url + 'public/images/yape/' + data.nombre;
        captionText.innerHTML = data.nombre;
      
		console.log(base_url + 'public/images/yape/' + data.nombre)
	}, 'json');
}

$('#btn_view').click(function (e) {
	e.preventDefault();
    let indexR = tablaImgTransf.row('.selected').id();
    if (indexR != null) {
		ver(indexR);
	} else {
		toastr.error('SELECCIONA UN REGISTRO PRIMERO', 'ERROR!');
	}
	
});



var span = document.getElementsByClassName("closeimg")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
  modal.style.display = "none";
}

function imprimir (fecha) {
	
	let url=base_url + 'pedidos/get_img/' + 0 + '/' + fecha;
	var myWindow=window.open(url);
	myWindow.focus();
	myWindow.print();
}

$('#btn_imprimir').click(function (e) {
	e.preventDefault();
	let fecha = $('#filtroFechaImg').val();
		
		if(fecha != ''){
			imprimir(fecha);
		}else{
			toastr.error('INGRESE UNA FECHA', 'ERROR!');
		}

});