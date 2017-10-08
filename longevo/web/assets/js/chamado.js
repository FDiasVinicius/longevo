function novoChamado(){
	location.href = "/chamado/novo";
}

function paginador(page){
	$("#filtro").attr("action", "/chamado/"+page);
	$("#filtro").submit();
}