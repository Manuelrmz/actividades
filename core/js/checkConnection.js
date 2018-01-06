(function(b)
{
	b.checkConnection = function(a)
	{
		function windowActive()
		{
			window.checkConnection.isActive = document.hidden || document.mozHidden || document.webkitHidden || document.msHidden?!1:!0
		}
		function startChecking(a)
		{
			console.log(a);
			if(window.checkConnection.isActive)
			{
				console.log('Ventana Activa');
			}
			else
			{
				console.log('Ventana No Activa');
			}
			window.checkConnection.isActive&&b.ajax({
				url:a,
				cache:!1,
				type:"HEAD",
				dataType:'script',
				success : function(response)
				{
					alert(response);
					console.log('Completado Correcto');
					window.checkConnection.connectionActive=!0;
				},error : function(xhr,ajaxOptions,thrownError)
				{
					console.log('Error');
					window.checkConnection.connectionActive=!1;
				},complete : function()
				{
					console.log('Always');
					if(window.checkConnection.connectionActive)
					{
						console.log("Conexion Activa");
					}
					else
					{
						console.log("Conexion Inactiva");
					}
				}

			});/*.done(function()
			{
				console.log("Correcto");
				window.checkConnection.connectionActive=!0;
			}).fail(function(e)
			{
				console.log("Fail");
				window.checkConnection.connectionActive=!1;
			}).always(function()
			{
				if(window.checkConnection.connectionActive)
				{
					console.log("Conexion Activa");
				}
				else
				{
					console.log("Conexion Inactiva");
				}
			});*/
			setTimeout(function(){startChecking(window.checkConnection.config.url)},window.checkConnection.config.interval);
		}
		a = a || {};
		a.url =  a.url||'http://www.c4yucatan.com.mx/scriptConexion.js';
		a.interval = a.interval || 5E3;
		a.message = a.message || "Conexion no detectada, Elementos de Envio Deshabilitados";
		console.log(a.url.indexOf("http"));
		-1===a.url.indexOf("http")&&(a.url = "http://"+a.url);
		window.checkConnection = 
		{
			isActive:!0,
			connectionActive:!0,
			config:a
		};
		"undefined"!==typeof document.hidden?document.addEventListener("visibilitychange",windowActive):"undefined"!==typeof document.webkitHidden?document.addEventListener("webkitvisibilitychange",windowActive):"undefined"!==typeof document.mozHidden?document.addEventListener("mozvisibilitychange",windowActive):"undefined"!==typeof document.msHidden&&document.addEventListener("msvisibilitychange",windowActive);
		startChecking(window.checkConnection.config.url)
	}
})(jQuery);