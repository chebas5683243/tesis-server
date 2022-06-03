<!doctype html>
<html lang="en">
<style type='text/css'>
	* {
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
		margin: 0;
		padding: 0;
		color: #4F4F4F;
	}

	.report {
		padding: 2rem 4rem;
	}

	.report-title {
		text-align: center;
		font-size: 1.25rem;
		font-weight: 700;
		margin-bottom: 1rem;
	}

	.report-section {
		margin-top: 3rem;
		font-size: .75rem;
	}

	.report-section--title-container {
		background-color: #DCDCDC;
		padding: .5rem;
	}

	.report-section--title {
		font-weight: 700;
	}

	.report-section--info {
		margin: 1rem 0;
		width: 100%;
	}

	.report-section--info-label {
		width: 30%;
		background-color: #EDEDED;
		border: 1px solid #DCDCDC;
		padding: .25rem .75rem;
	}

	.report-section--info-data {
		width: 70%;
		border: 1px solid #DCDCDC;
		padding: .25rem .75rem;
	}

	.report-section--table-container {
		margin: 1rem 0;
	}

	.report-section--table-title {
		font-weight: 700;
		margin-bottom: .5rem;
	}

	.report-section--table {
		width: 100%;
	}

	.report-section--table thead {
		background-color: #EDEDED;
	}

	.report-section--table thead th {
		padding: .25rem .75rem;
		border: #EDEDED;
	}

	.report-section--table tbody td {
		padding: .25rem .75rem;
		border: 1px solid #EDEDED;
	}
</style>
<head>
</head>
<body>
	<div class="report">
		<div class="report-title">Reporte Preliminar de Incidentes Ambientales</div>
		<div class="report-section">
			<div class="report-section--title-container">
				<span class="report-section--title">
					DATOS DEL ADMINISTRADO
				</span>
			</div>
			<table class="report-section--info">
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Nombre o razón social</span></td>
					<td class="report-section--info-data"><span>{{$company->razon_social}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>RUC</span></td>
					<td class="report-section--info-data"><span>{{$company->ruc}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Domilicio legal</span></td>
					<td class="report-section--info-data"><span>{{$company->direccion_fiscal}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Distrito</span></td>
					<td class="report-section--info-data"><span>{{$company->distrito_ciudad}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Provincia/Departamento</span></td>
					<td class="report-section--info-data"><span>{{$company->departamento}}</span></td>
				</tr>
			</table>
			<div class="report-section--table-container">
				<div class="report-section--table-title">
					Personas de contacto
				</div>
				<table class="report-section--table">
					<thead>
						<tr>
							<th style="width: 20%;">DNI</th>
							<th style="width: 40%;">Nombre</th>
							<th style="width: 20%;">Correo</th>
							<th style="width: 20%;">Teléfono</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="text-align: center;">{{$reporter->dni}}</td>
							<td>{{$reporter->primer_nombre . ' ' . $reporter->segundo_nombre . ' ' . $reporter->primer_apellido . ' ' . $reporter->segundo_apellido}}</td>
							<td style="text-align: center;">{{$reporter->email}}</td>
							<td style="text-align: center;">{{$reporter->numero_celular}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="report-section">
			<div class="report-section--title-container">
				<span class="report-section--title">
					DEL EVENTO
				</span>
			</div>
			<table class="report-section--info">
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Fecha</span></td>
					<td class="report-section--info-data"><span>{{$incident->fecha_incidente}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Hora de Inicio</span></td>
					<td class="report-section--info-data"><span>{{$incident->hora_incidente}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Lugar donde ocurrió</span></td>
					<td class="report-section--info-data"><span>{{$incident->detalle_ubicacion}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Coordenadas UTM Este</span></td>
					<td class="report-section--info-data"><span>{{$incident->coordenada_este}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Coordenadas UTM Norte</span></td>
					<td class="report-section--info-data"><span>{{$incident->coordenada_norte}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Localidad</span></td>
					<td class="report-section--info-data"><span>{{$incident->localidad}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Zona</span></td>
					<td class="report-section--info-data"><span>{{$incident->zona_sector}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Distrito</span></td>
					<td class="report-section--info-data"><span>{{$incident->distrito}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Provincia</span></td>
					<td class="report-section--info-data"><span>{{$incident->provincia}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Departamento</span></td>
					<td class="report-section--info-data"><span>{{$incident->departamento}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Descripción del evento</span></td>
					<td class="report-section--info-data"><span>{{$incident->detalle_evento}}</span></td>
				</tr>
			</table>
			<div class="report-section--table-container">
				<div class="report-section--table-title">
					Posibles causas origen del incidente ambiental
				</div>
				@if(count($causes) !== 0)
				<table class="report-section--table">
					<thead>
						<tr>
							<th style="width: 30%;">Tipo</th>
							<th style="width: 70%;">Descripción</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($causes as $cause)
						<tr>
							<td>{{$cause->tipo->label}}</td>
							<td>{{$cause->descripcion}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@else
				No hay causas registradas
				@endif
			</div>
			<div class="report-section--table-container">
				<div class="report-section--table-title">
					Plan de contingencia/Acciones inmediatas
				</div>
				@if(count($immediate_actions) !== 0)
				<table class="report-section--table">
					<thead>
						<tr>
							<th style="width: 30%;">Responsable</th>
							<th style="width: 70%;">Descripción</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($immediate_actions as $immediate_action)
						<tr>
							<td>{{$immediate_action->responsable}}</td>
							<td>{{$immediate_action->descripcion}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@else
				No hay causas registradas
				@endif
			</div>
		</div>

		<div class="report-section">
			<div class="report-section--title-container">
				<span class="report-section--title">
					DE LA PERSONA QUE REPORTA
				</span>
			</div>
			<table class="report-section--info">
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Nombres y Apellidos</span></td>
					<td class="report-section--info-data"><span>{{$reporter->primer_nombre . ' ' . $reporter->segundo_nombre . ' ' . $reporter->primer_apellido . ' ' . $reporter->segundo_apellido}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>DNI</span></td>
					<td class="report-section--info-data"><span>{{$reporter->dni}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Teléfono</span></td>
					<td class="report-section--info-data"><span>{{$reporter->numero_celular}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Correo</span></td>
					<td class="report-section--info-data"><span>{{$reporter->email}}</span></td>
				</tr>
				<tr class="report-section--info-row">
					<td class="report-section--info-label"><span>Cargo</span></td>
					<td class="report-section--info-data"><span>{{$reporter->cargo}}</span></td>
				</tr>
			</table>
		</div>
	</div>

</body>
</html>