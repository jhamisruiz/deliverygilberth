<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Pedidos extends MY_Controller
{

	public $pedidos;



	public function __construct()
	{

		parent::__construct();



		$this->load->model('pedidosModel');

		$this->pedidos = new PedidosModel;

		$this->load->model('orderModel');

		$this->order = new OrderModel;



		if (!isset($_SESSION['carrito'])) {

			$_SESSION['carrito'] = [];
		}
	}





	public function index()

	{

		$this->output->set_template('index');

		$this->load->css("public/css/jquery.dataTables.min.css");

		$this->load->js("public/js/jquery.dataTables.min.js");

		$this->load->js("public/js/pedidos/pedidos.js");

		$data = [];

		$_SESSION['edit'] = null;

		$data['grilla'] = $this->grid();

		$this->load->view('pedidos/index', $data);
	}







	public function add()

	{

		$this->output->set_template('index');

		$this->load->css("public/css/jquery.dataTables.min.css");

		$this->load->js("public/js/jquery.dataTables.min.js");

		$this->load->js("public/js/pedidos/pedidos_add.js");

		$data = [];

		$data['productos_carrito'] = $this->order->getCarrito($_SESSION['carrito']);

		$data['precio_delivery'] = $this->order->getPrecioDelivery();

		$data['productos'] = $this->pedidos->getAllProducts();

		$this->load->view('pedidos/add', $data);
	}



	public function edit($id)

	{

		$this->output->set_template('index');

		$this->load->css("public/css/jquery.dataTables.min.css");

		$this->load->js("public/js/jquery.dataTables.min.js");

		$this->load->js("public/js/pedidos/pedidos_add.js");

		$data = [];

		$data['pedido'] = $this->pedidos->getPedidoEdit($id);

		if (!isset($_SESSION['edit'])) {
			$this->setCarritoEdit($id);
		}

		$data['productos_carrito'] = $this->order->getCarrito($_SESSION['carrito']);

		$data['precio_delivery'] = $this->order->getPrecioDelivery();

		$data['productos'] = $this->pedidos->getAllProducts();

		$this->load->view('pedidos/edit', $data);
	}



	public function setCarritoEdit($id)
	{

		$_SESSION['carrito'] = [];

		$_SESSION['edit'] = 1;

		$q = $this->db->query("select p.id, p.precio, pp.cantidad, pp.detalles, pp.atributos from producto_pedido pp inner join producto p on pp.id_producto=p.id

			where pp.estado=1 and pp.id_pedido=" . $id);

		$res = $q->result();



		foreach ($res as $key => $producto) {

			$datos = array(

				'id_producto' => $producto->id,

				'cantidad' => $producto->cantidad,

				'nota' => $producto->detalles,

				'precio' => $producto->precio,

			);



			if (isset($producto->atributos)) {

				$datos['atributos'] = explode(",", $producto->atributos);
			} else {

				$datos['atributos'] = [];
			}



			array_push($_SESSION['carrito'], $datos);
		}
	}



	public function get($id)
	{

		$datos = $this->pedidos->find_pedido($id);

		$datos->productos = $this->pedidos->getProductos($id);

		echo json_encode($datos);
	}



	public function getEstado($id)
	{

		$datos = $this->pedidos->getEstado($id);

		echo json_encode($datos);
	}



	public function getCategorias()
	{

		echo json_encode($this->categorias->getCategorias());
	}



	private function grid()
	{

		$this->load->library('datatable');

		$this->datatable->setTabla('tablaPedidos');

		$this->datatable->setNroItem(true);

		$this->datatable->setAttrib('ajax', ['url' => base_url('pedidos/listaGrid')]);

		$this->datatable->setAttrib('language', ['url' => base_url("public/js/spanish.json")]);

		$this->datatable->setAttrib('select', true);

		$this->datatable->setAttrib('dom', 'frtip');

		$this->datatable->setAttrib('processing', false);

		$this->datatable->setAttrib('serverSide', true);

		$this->datatable->setAttrib('responsive', false);

		$this->datatable->setAttrib('lengthMenu', [[30, 50, 100, -1], [30, 50, 100, "Todos"]]);

		$this->datatable->setAttrib('order', [[1, 'desc']]);

		$this->datatable->setAttrib('columns', [

			['title' => 'F. Entrega', 'data' => "hora_entrega", 'name' => "hora_entrega"],

			['title' => 'Nombre', 'data' => "cliente", 'name' => "cliente"],

			['title' => 'Teléfono', 'data' => "telefono", 'name' => "telefono"],

			['title' => 'Dirección', 'data' => "direccion", 'name' => "direccion"],

			['title' => 'Referencia', 'data' => "referencia", 'name' => "referencia"],

			['title' => 'Total', 'data' => "total", 'name' => "total"],

			['title' => 'Motorizado', 'data' => "nombre", 'name' => "nombre"],

			['title' => 'Estado', 'data' => "condicion", 'name' => "condicion"],

		]);

		return $this->datatable->getJsGrid();
	}





	public function listaGrid()
	{

		$this->load->library('datatabledb');

		$this->datatabledb->setColumnaId('p.id');

		$this->datatabledb->setSelect("DATE_FORMAT(p.hora_entrega,'%d-%m-%Y %H:%i:%s') as hora_entrega, p.cliente, p.telefono, p.direccion, p.referencia, p.total, m.nombre, c.descripcion as condicion");

		$this->datatabledb->setFrom("pedido p left join motorizado m on p.motorizado=m.id inner join condicion c on p.condicion=c.id");

		$this->datatabledb->setWhere("p.estado", 1);

		$this->datatabledb->setOrder("p.id desc");

		$this->datatabledb->setParams($_GET);

		echo $this->datatabledb->getJson();
	}



	public function store()
	{

		$this->form_validation->set_rules('descripcion', 'descripcion', 'required');

		try {

			if ($this->form_validation->run() === FALSE) {

				throw new \Exception(validation_errors());
			}

			$response = [];

			$id = $this->input->post("id");

			if ($id === "") {

				$res = $this->categorias->insert_categoria();
			} else {

				$res = $this->categorias->update_categoria($id);
			}

			$response["error"] = $res;

			echo json_encode($response);
		} catch (\Exception $e) {

			echo json_encode(["error" => true, "message" => $e->getMessage()]);
		}
	}



	public function modificar()
	{

		$id = $this->input->post("cod");

		$res = $this->pedidos->modificar_pedido($id);

		$response["error"] = $res;

		echo json_encode($response);
	}



	public function delete($id)
	{

		$response = [];

		$response["error"] = !$this->pedidos->delete_pedidos($id);

		echo json_encode($response);
	}

	//#Wilson, funcion para obtener la imagen de la url
	public function downloadyape()
	{
		if ($_POST['url']) {
			$nombreimg = (md5($_POST['url']));

			$img_file = $_POST['url'];
			$img_file = file_get_contents($img_file);


			$count = $this->pedidos->getImageYape($nombreimg . '.jpg');

			if ((int)$count[0]->count == 0) {
				$file_loc = $_SERVER['DOCUMENT_ROOT'] . '/public/images/yape/' . $nombreimg . '.jpg';
				$file_handler = fopen($file_loc, 'w');

				if (fwrite($file_handler, $img_file)) {

					$idimg = $this->pedidos->insert_yape($nombreimg . '.jpg');
					if ($idimg) {
						echo json_encode(array('img' => $nombreimg . '.jpg', 'msg' => 'Se registro correctamente', 'error' => ''));
					} else {
						echo json_encode(array('img' => '', 'msg' => '', 'error' => 'Intentelo nuevamente'));
					}
				}

				fclose($file_handler);
			} else {
				echo json_encode(array('error' => 'La imagen ya existe'));
			}
		}
	}

	//#Wilson, end

	//#Wilson, consulta prpra obtener las imagenes datatable 
	public function listaGridImg()
	{
		$this->load->library('datatabledb');
		$this->datatabledb->setColumnaId('i.id');
		$this->datatabledb->setSelect("i.nombre,DATE_FORMAT(i.fecha,'%d-%m-%Y %H:%i:%s') as Fecha");
		$this->datatabledb->setFrom('imgtrasnferencia i');
		$this->datatabledb->setOrder("i.id desc");

		$this->datatabledb->setParams($_GET);
		echo $this->datatabledb->getJson();
	}
	//#Wilson, end

	//#Wilson, Generar datatable imagenes
	private function gridImg()
	{
		$this->load->library('datatable');
		$this->datatable->setTabla('tablaImgTransf');
		$this->datatable->setNroItem(true);
		$this->datatable->setAttrib('ajax', ['url' => base_url('pedidos/listaGridImg')]);
		$this->datatable->setAttrib('language', ['url' => base_url("public/js/spanish.json")]);
		$this->datatable->setAttrib('select', true);
		$this->datatable->setAttrib('dom', 'frtip');
		$this->datatable->setAttrib('processing', false);
		$this->datatable->setAttrib('serverSide', true);
		$this->datatable->setAttrib('responsive', false);
		$this->datatable->setAttrib('lengthMenu', [[30, 50, 100, -1], [30, 50, 100, "Todos"]]);
		$this->datatable->setAttrib('order', [[1, 'desc']]);
		$this->datatable->setAttrib('columns', [
			['title' => 'Nombre', 'data' => "nombre", 'name' => "nombre"],
			['title' => 'Fecha', 'data' => "Fecha", 'name' => "Fecha"],
		]);
		return $this->datatable->getJsGrid();
	}
	//#Wilson, end

	//#Wilson view lista de imagenes
	public function imagenes_transferencias()
	{
		$this->load->library('datatabledb');
		$this->output->set_template('index');
		$this->load->css("public/css/jquery.dataTables.min.css");
		$this->load->css("public/css/modal_img.css");
		$this->load->js("public/js/jquery.dataTables.min.js");
		$this->load->js("public/js/pedidos/img_yape.js");


		$data['grilla'] = $this->gridImg();

		$this->load->view('pedidos/imagenes_transferencias', $data);
	}
	//#Wilson, end

	//#Wilson, para ver imagen e imprimir
	public function get_img($id = null, $fecha = null)
	{

		$this->db->select('*');
		$this->db->from('imgtrasnferencia i');
		if ($id != 0) {
			$this->db->where('i.id=', $id);
		}
		if ($fecha != null) {
			$datos = array();
			$this->db->where('DATE(i.fecha)=', "$fecha");

			$q = $this->db->get();
			while ($dat = $q->result_id->fetch_object()) {
				$dat->fechahoy = $fecha;
				array_push($datos, $dat);
			}
			
			$this->pdf($datos);
		}

		$q = $this->db->get();
		$datos = $q->row();
		echo json_encode($datos);
	}
	//#Wilson, end

	//#Wilson, generar pdf
	public function pdf($datos)
	{
		$this->load->library('pdfgenerator');
 
		$data['datos']=$datos;
		
	    $html = $this->load->view('pdfyape', $data, true);
	    
	    $this->pdfgenerator->generate($html,'contoh');

	/*	$empresa = $this->db->get_where('empresa', ['id' => 1])->row();
		$logo = "public/uploads/logo/" . $empresa->logo;

		$this->load->library("Fpdf");

		$pdf = new Fpdf('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 8);    //Letra Arial, negrita (Bold), tam. 20
		$textypos = 5;
		for ($i = 0; $i < count($datos); $i++) {
			$img =$_SERVER['DOCUMENT_ROOT'] . '/public/images/yape/' . $datos[$i]->nombre;
			$pdf->Image($img,(5*($i*13)),5,50);
		}
		

		$pdf->output();*/
	}
	//#Wilson, end
}
