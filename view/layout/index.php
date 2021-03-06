<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="cache-control" content="no-store" />
		<meta http-equiv="expires" content="-1" />
		<!-- <link rel="shortcut icon" href="/<?php //echo BASE_DIR;?>/images/c4icon.ico"> -->
		<link rel="stylesheet" href="/<?php echo BASE_DIR;?>/view/layout/index.css">
		<link rel="stylesheet" href="/<?php echo BASE_DIR; ?>/core/kendo/styles/kendo.common.min.css">
		<link rel="stylesheet" href="/<?php echo BASE_DIR; ?>/core/kendo/styles/kendo.default.min.css">
		<link rel="stylesheet" href="/<?php echo BASE_DIR; ?>/core/css/cssLib.css">
		<script>var path = '/actividadesc4/';</script>
		<script type="text/javascript" charset="utf-8" src="/<?php echo BASE_DIR;?>/core/js/jquery.js"></script>
		<script type="text/javascript" charset="utf-8" src="/<?php echo BASE_DIR;?>/core/js/library.js"></script>
		<title>#TITULO#</title>
	</head>
	<body>
		<header>
			<?php
			if(isset($_SESSION["userData"]))
			{
			?>
			<div id="iconMenu" class="iconMenu" onclick="toogleMenuIcon(this)">
				<div class="line1"></div>
				<div class="line2"></div>
				<div class="line3"></div>
			</div>
			<?php
			}	
			?>
			<a id="headerTitle" href="/<?php echo BASE_DIR;?>"><img src="/<?php echo BASE_DIR;?>/images/c4.gif" alt="" height="35px">Centro de Comando, Control, Comunicaciones y Computo</a>
			<?php
			if(isset($_SESSION["userData"]))
			{
			?>
			<div id="btnEndSession">
				<a href="/<?php echo BASE_DIR;?>/usuarios/logout">Cerrar Sesion</a>
			</div>
			<?php
			}
			?>
		</header>
		<?php
		if(isset($_SESSION["userData"]))
		{
		?>
		<nav>
			<div id="sidebar" class="sidebar">
				<div class="menu userRow">
					<a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION["userData"]["nombres"] ?></a>
					<div class="submenu">
						<div class="submenuoption">
							<a href="/<?php echo BASE_DIR;?>/usuarios/cambiarcontrasenia">Cambiar Contrase&ntilde;a</a>
						</div>
					</div>
				</div>
				<div class="menu"><a href="/<?php echo BASE_DIR;?>"><span class="glyphicon glyphicon-home"></span>Inicio</a></div>
				<?php
				if($_SESSION["userData"]["servicios"] == 1)
				{
				?>
					<div class="menu"><a href="/<?php echo BASE_DIR;?>/servicios"><span class="glyphicon glyphicon-file"></span>Servicios</a></div>
				<?php
				}
				if($_SESSION["userData"]["factura"] == 1 || $_SESSION["userData"]["proveedor"] == 1 || $_SESSION["userData"]["inventario"] == 1 || $_SESSION["userData"]["catalogoFacturas"] == 1 || $_SESSION["userData"]["resguardos"] == 1 || $_SESSION["userData"]["recibos"] == 1)
				{
				?>
					<div class="menu"><a href="#"><span class="glyphicon glyphicon-menu-right"></span>Gestion de Activos</a>
						<div class="submenu">
						<?php
						if($_SESSION["userData"]["factura"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/facturas"><span class="glyphicon glyphicon-inbox"></span>Facturas</a></div>
						<?php
						}
						if($_SESSION["userData"]["proveedor"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/proveedores"><span class="glyphicon glyphicon-list"></span>Proveedores</a></div>
						<?php
						}
						if($_SESSION["userData"]["inventario"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/inventario"><span class="glyphicon glyphicon-list"></span>Inventario</a></div>
						<?php
						}
						if($_SESSION["userData"]["resguardos"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/resguardos"><span class="glyphicon glyphicon-inbox"></span>Resguardos</a></div>
						<?php
						}
						if($_SESSION["userData"]["recibos"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/recibos"><span class="glyphicon glyphicon-inbox"></span>Recibos</a></div>
						<?php
						}
						if($_SESSION["userData"]["catalogoFacturas"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/catalogo/facturas"><span class="glyphicon glyphicon-list"></span>Catalogos</a></div>
						<?php
						}
						?>
						</div>
					</div>
				<?php
				}
				if($_SESSION["userData"]["radiosmantto"] == 1 || $_SESSION["userData"]["radioscat"] == 1 || $_SESSION["userData"]["equiposradios"] == 1 || $_SESSION["userData"]["rptvisitasitio"] == 1 || $_SESSION["userData"]["reporteadorradios"] == 1)
				{
				?>
					<div class="menu"><a href="#"><span class="glyphicon glyphicon-signal"></span>Radios</a>
						<div class="submenu">
						<?php
						if($_SESSION["userData"]["radiosmantto"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/manttoradios"><span class="glyphicon glyphicon-cog"></span>Servicios Taller</a></div>
						<?php
						}
						if($_SESSION["userData"]["radioscat"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/catalogo/radios"><span class="glyphicon glyphicon-list"></span>Catalogos de Servicio</a></div>
						<?php
						}
						if($_SESSION["userData"]["equiposradios"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/radios/equipos"><span class="glyphicon glyphicon-list-alt"></span>Base de Datos Radios</a></div>
						<?php
						}
						if($_SESSION["userData"]["rptvisitasitio"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/radios/visitasitio"><span class="glyphicon glyphicon-road"></span>Visitas a sitio</a></div>
						<?php
						}
						if($_SESSION["userData"]["reporteadorradios"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/radios/reporteador"><span class="glyphicon glyphicon-list-alt"></span>Reporteador</a></div>
						<?php
						}
						if($_SESSION["userData"]["sitioscat"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/sitios"><span class="glyphicon glyphicon-list"></span>Edicion de sitios</a></div>
						<?php
						}
						if($_SESSION["userData"]["sitiosconsu"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/sitios/consulta"><span class="glyphicon glyphicon-list-alt"></span>Sitios de Comunicaciones</a></div>
						<?php
						}
						?>
						</div>
					</div>
				<?php
				}
				if($_SESSION["userData"]["usuariosadmon"] == 1 || $_SESSION["userData"]["catalogoGeneral"] == 1)
				{
				?>
					<div class="menu"><a href="#"><span class="glyphicon glyphicon-cog"></span>Herramientas</a>
						<div class="submenu">
						<?php
						if($_SESSION["userData"]["usuariosadmon"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/usuarios"><span class="glyphicon glyphicon-user"></span>Administrar Usuarios</a></div>
						<?php
						}
						if($_SESSION["userData"]["catalogoGeneral"] == 1)
						{
						?>
							<div class="submenuoption"><a href="/<?php echo BASE_DIR;?>/catalogo"><span class="glyphicon glyphicon-list"></span>Catalogos Generales</a></div>
						<?php
						}
						?>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</nav>
		<?php
		}
		?>
		<main>
			<div id="mainContainer" class="clear">
				#CUERPO#
			</div>
			<footer class="clear">
				<div id="mensajePie">&copy;&nbsp;C-4 Control, Comando, Comunicaciones y Cómputo</div>
		    	<div id="fechaPie"><script>fechaActual();</script></div>
			</footer>
			<div class="alert-container"></div>
		</main>
		<?php
		if(isset($_SESSION["userData"]))
		{
		?>
		<script type="text/javascript">
			var menuOpened = true;
			var minSize = false;
			function toogleMenuIcon(x) 
			{
				x.classList.toggle("change");
				document.getElementById("sidebar").style.width = menuOpened ? "0px" : "200px";
				if(this.innerWidth >= 1201)
					document.getElementsByTagName("main")[0].style.marginLeft = menuOpened ? "0px" : "200px";
				menuOpened = !menuOpened;
			}
			function checkWindowsize()
			{
				if(this.innerWidth < 1201 && !minSize)
				{
					document.getElementById("iconMenu").classList.remove("change");
					document.getElementById("sidebar").style.width = "0px";
					document.getElementsByTagName("main")[0].style.marginLeft = "0px";
					menuOpened = false;
					minSize = true;
				}
				else
				{
					if(this.innerWidth >= 1201)
					{
						minSize = false;
						document.getElementsByTagName("main")[0].style.marginLeft = menuOpened ? "200px" : "0px";
					}
					if(menuOpened)
						document.getElementById("iconMenu").classList.add("change");
				}
			}
			window.addEventListener("resize",checkWindowsize);
			window.addEventListener("load",checkWindowsize);
		</script>
		<?php
		}
		?>
	</body>
</html>
