<?php
include_once CORE_PATH."PHPExcel/Classes/PHPExcel/IOFactory.php";
include_once CORE_PATH."PHPExcel/Classes/PHPExcel.php";
include_once CORE_PATH."PHPExcel/Classes/PHPExcel/Writer/Excel2007.php";
class reporteadorController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function saveData()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $_SESSION["reportdata"] = $_POST;
        echo json_encode(array('ok'=>true));
	}
	public function generarReporte()
	{
        $this->reporteador();
        switch($this->_data["tipoReporte"])
        {
        	case 1:
        		$this->radiosCantidadPorServicio();
        	break;
        	case 2:
        		$this->radiosIntervencionesSitioTotalizado();
        	break;
        	default:
        		echo "El reporte que solicitaste no existe o no tienes permiso para generarlo";
        	break;
        }
	}
	public function radiosCantidadPorServicio()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('reporteadorradios');
        $totalAux = 0;
        $condi = true;
        $condi = $condi && $this->_validar->Date($this->_data["fechaInicio"],"Fecha de Inicio");
        $condi = $condi && $this->_validar->Date($this->_data["fechaFin"],"Fecha Final");
        if($condi)
        {
			$xls = new PHPExcel();
			//Por Dependencia
			$xls->setActiveSheetIndex(0);			
			$xls->getActiveSheet()->mergeCells("C3:F3");
			$xls->getActiveSheet()->getStyle("C3:F3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3:F3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFont()->setSize(12);
			$xls->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C3","CANTIDAD POR DEPENDENCIA");
			$xls->getActiveSheet()->mergeCells("C4:F4");
			$xls->getActiveSheet()->getStyle("C4:F4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4:F4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFont()->setSize(10);
			$xls->getActiveSheet()->getRowDimension(4)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C4","PERIODO: ".$this->_data["fechaInicio"]." - ".$this->_data["fechaFin"]);
			$servxdepen = manttoradios::select(array('depen.nombre','count(*)'=>'total'))->join(array('dependencias','depen'),'manttoradios.dependencia','=','depen.id','LEFT')->where('manttoradios.fechaAlta','>=',$this->_data["fechaInicio"])->where('manttoradios.fechaAlta','<=',$this->_data["fechaFin"])->Groupby('depen.nombre')->get()->fetch_all();
			if(sizeof($servxdepen) > 0 && $servxdepen[0]["nombre"] != NULL)
			{
				$xls->getActiveSheet()->SetCellValue('A9',"DEPENDENCIA");
				$xls->getActiveSheet()->SetCellValue('B9',"CANTIDAD");
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(12);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(12);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->getStartColor()->setARGB('2D3605');
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->getColor()->setRGB("FFFFFF");
				$xls->getActiveSheet()->getStyle("A9:B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setBold(true);
				$row = 10;
				foreach ($servxdepen as $value) 
				{
					$xls->getActiveSheet()->SetCellValue('A'.$row,$value["nombre"]);
					$xls->getActiveSheet()->SetCellValue('B'.$row,$value["total"]);
					$totalAux += $value["total"];
					$row++;
				}
				$xls->getActiveSheet()->getStyle("A10:B".($row-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$xls->getActiveSheet()->SetCellValue('A'.$row,"TOTAL");
				$xls->getActiveSheet()->SetCellValue('B'.$row,$totalAux);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				//Creamos Chart 
				$dataLabel = array(new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$9', null, 1));
				$xTicks = array(new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$10:$A$'.($row-1), null, ($row-10)));
				$dataValues = array(new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$10:$B$'.($row-1), null, ($row-10)));
				$series1 = new PHPExcel_Chart_DataSeries(PHPExcel_Chart_DataSeries::TYPE_PIECHART,PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
					range(0, count($dataValues)-1),$dataLabel,$xTicks,$dataValues);
				$layout1 = new PHPExcel_Chart_Layout();
				$layout1->setShowVal(TRUE);
				$layout1->setShowPercent(TRUE);
				$plotarea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
				$legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
				$title1 = new PHPExcel_Chart_Title('Grafica por Dependencia');
				$chart1 = new PHPExcel_Chart('Grafica por Dependencia',$title1,$legend1,$plotarea1,true,0,null,null);
				//	Set the position where the chart should appear in the worksheet
				$chart1->setTopLeftPosition('D9');
				$chart1->setBottomRightPosition('L29');
				$xls->getActiveSheet()->addChart($chart1);
			}
			//Por Tipo de Falla
			$xls->createSheet();
			$xls->setActiveSheetIndex(1);			
			$xls->getActiveSheet()->mergeCells("C3:F3");
			$xls->getActiveSheet()->getStyle("C3:F3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3:F3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:F3")->getFont()->setSize(12);
			$xls->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C3","SERVICIOS POR TIPO DE FALLA");
			$xls->getActiveSheet()->mergeCells("C4:F4");
			$xls->getActiveSheet()->getStyle("C4:F4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4:F4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:F4")->getFont()->setSize(10);
			$xls->getActiveSheet()->getRowDimension(4)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C4","PERIODO: ".$this->_data["fechaInicio"]." - ".$this->_data["fechaFin"]);
			$servxmantto = manttoradios::select(array('mantto.nombre','count(*)'=>'total'))->join(array('mantenimientos','mantto'),'manttoradios.mantenimiento','=','mantto.id','LEFT')->where('manttoradios.fechaAlta','>=',$this->_data["fechaInicio"])->where('manttoradios.fechaAlta','<=',$this->_data["fechaFin"])->Groupby('mantto.nombre')->get()->fetch_all();
			if(sizeof($servxmantto) > 0 && $servxmantto[0]["nombre"] != NULL)
			{
				$totalAux = 0;
				$xls->getActiveSheet()->SetCellValue('A9',"TIPO");
				$xls->getActiveSheet()->SetCellValue('B9',"CANTIDAD");
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(12);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(12);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->getStartColor()->setARGB('2D3605');
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->getColor()->setRGB("FFFFFF");
				$xls->getActiveSheet()->getStyle("A9:B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setBold(true);
				$row = 10;
				foreach ($servxmantto as $value) 
				{
					$xls->getActiveSheet()->SetCellValue('A'.$row,$value["nombre"]);
					$xls->getActiveSheet()->SetCellValue('B'.$row,$value["total"]);
					$totalAux += $value["total"];
					$row++;
				}
				$xls->getActiveSheet()->getStyle("A10:B".($row-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$xls->getActiveSheet()->SetCellValue('A'.$row,"TOTAL");
				$xls->getActiveSheet()->SetCellValue('B'.$row,$totalAux);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				//Creamos Chart 
				$currentWorkseet = "Worksheet 1";
				$dataLabel = array(new PHPExcel_Chart_DataSeriesValues('String', "'".$currentWorkseet."'".'!$B$9', null, 1));
				$xTicks = array(new PHPExcel_Chart_DataSeriesValues('String', "'".$currentWorkseet."'".'!$A$10:$A$'.($row-1), null, ($row-10)));
				$dataValues = array(new PHPExcel_Chart_DataSeriesValues('Number', "'".$currentWorkseet."'".'!$B$10:$B$'.($row-1), null, ($row-10)));
				$series1 = new PHPExcel_Chart_DataSeries(PHPExcel_Chart_DataSeries::TYPE_BARCHART,PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
					range(0, count($dataValues)-1),$dataLabel,$xTicks,$dataValues);
				$series1->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
				$plotarea1 = new PHPExcel_Chart_PlotArea(null, array($series1));
				$legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
				$title1 = new PHPExcel_Chart_Title('Grafica por Tipo de Falla');
				$chart1 = new PHPExcel_Chart('Grafica por Tipo de Falla',$title1,$legend1,$plotarea1,true,0,null,null);
				$chart1->setTopLeftPosition('D9');
				$chart1->setBottomRightPosition('L29');
				$xls->getActiveSheet()->addChart($chart1);
			}
			//Por Tipo de Equipo
			$xls->createSheet();
			$xls->setActiveSheetIndex(2);			
			$xls->getActiveSheet()->mergeCells("C3:J3");
			$xls->getActiveSheet()->getStyle("C3:J3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3:J3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:J3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C3:J3")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C3:J3")->getFont()->setSize(12);
			$xls->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C3","CANTIDAD DE SERVICIOS POR MODELO DE TERMINAL");
			$xls->getActiveSheet()->mergeCells("C4:J4");
			$xls->getActiveSheet()->getStyle("C4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4:J4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$xls->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:J4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$xls->getActiveSheet()->getStyle("C4:J4")->getFont()->setBold(true);
			$xls->getActiveSheet()->getStyle("C4:J4")->getFont()->setSize(10);
			$xls->getActiveSheet()->getRowDimension(4)->setRowHeight(25);
			$xls->getActiveSheet()->SetCellValue("C4","PERIODO: ".$this->_data["fechaInicio"]." - ".$this->_data["fechaFin"]);
			$servxtiporad = manttoradios::select(array('tr.descripcion','count(*)'=>'total'))
										->join(array('equiposradios','er'),'manttoradios.rfsi','=','er.rfsi','LEFT')
										->join(array('tipoequiporadios','tr'),'er.tipo','=','tr.clave','LEFT')
										->where('manttoradios.fechaAlta','>=',$this->_data["fechaInicio"])
										->where('manttoradios.fechaAlta','<=',$this->_data["fechaFin"])
										->Groupby('tr.descripcion')->get()->fetch_all();
			if(sizeof($servxtiporad) > 0 && $servxtiporad[0]["descripcion"] != NULL)
			{
				$totalAux = 0;
				$xls->getActiveSheet()->SetCellValue('A9',"MODELO");
				$xls->getActiveSheet()->SetCellValue('B9',"CANTIDAD");
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(12);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(12);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->getStartColor()->setARGB('2D3605');
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->getColor()->setRGB("FFFFFF");
				$xls->getActiveSheet()->getStyle("A9:B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setBold(true);
				$row = 10;
				foreach ($servxtiporad as $value) 
				{
					$xls->getActiveSheet()->SetCellValue('A'.$row,$value["descripcion"]);
					$xls->getActiveSheet()->SetCellValue('B'.$row,$value["total"]);
					$totalAux += $value["total"];
					$row++;
				}
				$xls->getActiveSheet()->getStyle("A10:B".($row-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$xls->getActiveSheet()->SetCellValue('A'.$row,"TOTAL");
				$xls->getActiveSheet()->SetCellValue('B'.$row,$totalAux);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				//Creamos Chart 
				$currentWorkseet = "Worksheet 2";
				$dataLabel = array(new PHPExcel_Chart_DataSeriesValues('String', "'".$currentWorkseet."'".'!$B$9', null, 1));
				$xTicks = array(new PHPExcel_Chart_DataSeriesValues('String', "'".$currentWorkseet."'".'!$A$10:$A$'.($row-1), null, ($row-10)));
				$dataValues = array(new PHPExcel_Chart_DataSeriesValues('Number', "'".$currentWorkseet."'".'!$B$10:$B$'.($row-1), null, ($row-10)));
				$series1 = new PHPExcel_Chart_DataSeries(PHPExcel_Chart_DataSeries::TYPE_BARCHART,PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
					range(0, count($dataValues)-1),$dataLabel,$xTicks,$dataValues);
				$series1->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
				$plotarea1 = new PHPExcel_Chart_PlotArea(null, array($series1));
				$legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
				$title1 = new PHPExcel_Chart_Title('Grafica por Modelo de Terminal');
				$chart1 = new PHPExcel_Chart('Grafica por Modelo de Terminal',$title1,$legend1,$plotarea1,true,0,null,null);
				$chart1->setTopLeftPosition('D9');
				$chart1->setBottomRightPosition('L29');
				$xls->getActiveSheet()->addChart($chart1);
			}
			$xls->setActiveSheetIndex(0);		
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=reporte.xlsx");
			header('Cache-Control: max-age=0');
			$guardarExcel = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$guardarExcel->setIncludeCharts(TRUE);
			$guardarExcel->save("php://output");
        }	
        else
        	echo $this->_validar->getWarnings();
	}
	public function radiosIntervencionesSitioTotalizado()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('reporteadorradios');
        $condi = true;
        $totalAux = 0;
        $condi = $condi && $this->_validar->Date($this->_data["fechaInicio"],"Fecha de Inicio");
        $condi = $condi && $this->_validar->Date($this->_data["fechaFin"],"Fecha Final");
        if($condi)
        {
        	$visitasSitio = visitasitios::select(array('sitios'))->where('fechaVisita','>=',$this->_data["fechaInicio"])
        					->where('fechaVisita','<=',$this->_data["fechaFin"])->get()->fetch_all();
        	if($visitasSitio)
        	{
        		$sitios = "";
        		foreach ($visitasSitio as $value) 
        		{
        			$sitios.= $sitios == "" ? $value["sitios"] : ",".$value["sitios"];
        		}
        		$sitios = explode(',',$sitios);
        		$sitiosAux = array();
        		for($i = 0; $i < count($sitios); $i++)
        		{
        			if(isset($sitiosAux[$sitios[$i]]))
        				$sitiosAux[$sitios[$i]]++;
        			else
        				$sitiosAux[$sitios[$i]] = 1;
        		};
        		$sitios = sitios::select(array('nombre','id'))->whereIn('id',array_keys($sitiosAux))->get()->fetch_all();
        		$xls = new PHPExcel();
				//Por Sitio
				$xls->setActiveSheetIndex(0);			
				$xls->getActiveSheet()->mergeCells("C3:J3");
				$xls->getActiveSheet()->getStyle("C3:J3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("C3:J3")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$xls->getActiveSheet()->getStyle("C3")->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("C3:J3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("C3:J3")->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("C3:J3")->getFont()->setSize(12);
				$xls->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
				$xls->getActiveSheet()->SetCellValue("C3","REPORTE TOTALIZADO DE INTERVENCIONES POR SITIO");
				$xls->getActiveSheet()->mergeCells("C4:J4");
				$xls->getActiveSheet()->getStyle("C4:J4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("C4:J4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$xls->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("C4:J4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("C4:J4")->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("C4:J4")->getFont()->setSize(10);
				$xls->getActiveSheet()->getRowDimension(4)->setRowHeight(25);
				$xls->getActiveSheet()->SetCellValue("C4","PERIODO: ".$this->_data["fechaInicio"]." - ".$this->_data["fechaFin"]);
				$xls->getActiveSheet()->SetCellValue('A9',"SITIO");
				$xls->getActiveSheet()->SetCellValue('B9',"CANTIDAD");
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(12);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(12);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFill()->getStartColor()->setARGB('2D3605');
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->getColor()->setRGB("FFFFFF");
				$xls->getActiveSheet()->getStyle("A9:B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$xls->getActiveSheet()->getStyle("A9:B9")->getFont()->setBold(true);
				$row = 10;
				foreach ($sitios as $value) 
				{
					$xls->getActiveSheet()->SetCellValue('A'.$row,$value["nombre"]);
					$xls->getActiveSheet()->SetCellValue('B'.$row,$sitiosAux[$value["id"]]);
					$totalAux += $sitiosAux[$value["id"]];
					$row++;
				}
				$xls->getActiveSheet()->getStyle("A10:B".($row-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$xls->getActiveSheet()->SetCellValue('A'.$row,"TOTAL");
				$xls->getActiveSheet()->SetCellValue('B'.$row,$totalAux);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setBold(true);
				$xls->getActiveSheet()->getStyle("A".$row.":B".$row)->getFont()->setSize(9);
				$xls->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				//Creamos Chart 
				$dataLabel = array(new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$9', null, 1));
				$xTicks = array(new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$10:$A$'.($row-1), null, ($row-10)));
				$dataValues = array(new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$10:$B$'.($row-1), null, ($row-10)));
				$series1 = new PHPExcel_Chart_DataSeries(PHPExcel_Chart_DataSeries::TYPE_PIECHART,PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
					range(0, count($dataValues)-1),$dataLabel,$xTicks,$dataValues);
				$layout1 = new PHPExcel_Chart_Layout();
				$layout1->setShowVal(TRUE);
				$layout1->setShowPercent(TRUE);
				$plotarea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
				$legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
				$title1 = new PHPExcel_Chart_Title('Grafica por intervencio a sitio');
				$chart1 = new PHPExcel_Chart('Grafica por intervencio a sitio',$title1,$legend1,$plotarea1,true,0,null,null);
				//	Set the position where the chart should appear in the worksheet
				$chart1->setTopLeftPosition('D9');
				$chart1->setBottomRightPosition('L29');
				$xls->getActiveSheet()->addChart($chart1);
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=reporte.xlsx");
				header('Cache-Control: max-age=0');
				$guardarExcel = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
				$guardarExcel->setIncludeCharts(TRUE);
				$guardarExcel->save("php://output");
        	}
        }
        else
        	echo $this->_validar->getWarnings();
	}
	public function reporteador()
	{
		$this->_data["tipoReporte"] = isset($_SESSION["reportdata"]["tipoReporte"]) ? (integer)$_SESSION["reportdata"]["tipoReporte"] : 0;
		$this->_data["fechaInicio"] = isset($_SESSION["reportdata"]["fechaInicio"]) ? $_SESSION["reportdata"]["fechaInicio"] : "";
		$this->_data["fechaFin"] = isset($_SESSION["reportdata"]["fechaFin"]) ? $_SESSION["reportdata"]["fechaFin"] : "";
	}
}