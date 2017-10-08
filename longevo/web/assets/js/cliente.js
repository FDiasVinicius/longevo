function novoCliente(){
	location.href = "/cliente/novo";
}

function paginador(page){
	$("#filtro").attr("action", "/cliente/"+page);
	$("#filtro").submit();
}