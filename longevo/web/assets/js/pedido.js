function novoPedido(){
	location.href = "/pedido/novo";
}

function paginador(page){
	$("#filtro").attr("action", "/pedido/"+page);
	$("#filtro").submit();
}