<style>
	.sidenav {
	    height: 100%;
	    width: 0;
	    position: fixed;
	    z-index: 1;
	    top: 0;
	    left: 0;
	    background-color: rgb(248,248,252);
	    overflow-x: hidden;
	    transition: 0.5s;
	    padding-top: 60px;
	    text-align: left;
	}

	.sidenav a {
	    padding: 10px;
	    text-decoration: none;
	    font-size: 20px;
	    color: #818181;
	    display: block;
	    transition: 0.3s;
	}

	.sidenav a:hover {
	    color: rgb(63,63,148);
	    text-decoration: underline;
	}

	.sidenav .closebtn {
	    position: absolute;
	    top: 0;
	    right: 25px;
	    font-size: 36px;
	    margin-left: 50px;
	}
	
	.btn-menu{
		position: absolute;
		top:2.3%; 
		left:1%;
		border-radius: 2px;
	}

	@media screen and (max-height: 450px) {
	  .sidenav {padding-top: 15px;}
	  .sidenav a {font-size: 18px;}
	}
</style>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <br><br>
  <a href="moderador">Acceder a un Concurso</a>
  <a href="obtener_excel">Obtener Excel De Resultados</a>
  <a href="crear">Crear Un Concurso Nuevo</a>
  <a href="inicio">Inicio Concursante</a>
  <a href="restablecer">Restablecer Concurso</a>
</div>

<span class="btn btn-geo btn-menu" onclick="openNav()">&#9776;</span>

<script>
	function openNav() {
	    document.getElementById("mySidenav").style.width = "300px";
	}

	function closeNav() {
	    document.getElementById("mySidenav").style.width = "0";
	}
</script>
     