<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Scan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_scan','record');
    }
    
    function index()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        $this->load->view('v_scan');  
    } 
    
    function create()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $id     = addslashes($_POST['scid']);
        $shid   = addslashes($_POST['shid']);
        $mcid   = addslashes($_POST['mcid']);
        $procid = $this->session->userdata('procid');
        $proc   = $this->session->userdata('proc');
        $nik    = $this->session->userdata('nik');
                           
        echo $this->record->create($id, $procid, $proc, $nik, $shid, $mcid);
        
    }
    
    function machCheck()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $liid   = addslashes($_POST['liid']);
        $mcid   = addslashes($_POST['mcid']);
        $procid = $this->session->userdata('procid');
                           
        echo $this->record->machCheck($procid, $liid, $mcid);
        
    }
    
    function exportExcel(){
        $auth   = new Auth();
        $auth->restrict();
        
        //$auth->cek_menu(14);
        
        //define('FPDF_FONTPATH',$this->config->item('fonts_path'));
        //$data = $this->record->exportExcel($_GET['nilai']);
        $data = $this->record->exportExcel();
        $this->load->view('v_export_excel.php',$data);
    }
    //////////////////////////////////
    function update()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $t_po_header_no_old = addslashes($_POST['t_po_header_no_old']);
        $t_po_header_no     = addslashes($_POST['t_po_header_no']);
        $t_po_header_cust   = addslashes($_POST['t_po_header_cust']); 
        $t_po_header_date   = addslashes($_POST['t_po_header_date']);
        
        echo $this->record->update($t_po_header_no_old, $t_po_header_no, $t_po_header_cust, $t_po_header_date);
        
    }
        
    function delete()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $t_po_header_no          = addslashes($_POST['t_po_header_no']);
        
        echo $this->record->delete($t_po_header_no);
        
    }
    
    
    //--DETAIL--//
    function detailIndex()
    {
        $auth       = new Auth();
         // mencegah user yang belum login untuk mengakses halaman ini
        $auth->restrict();
        
        if (isset($_GET['grid'])) 
        {
            echo $this->record->detailIndex($_GET['nilai']);   
        }
        else 
        {
            $this->load->view('transaksi/po/v_po');  
        }
    }
    
    function detailCreate()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $t_po_detail_no             = addslashes($_POST['t_po_detail_no']);
        $t_po_detail_date           = addslashes($_POST['t_po_detail_date']); 
        $t_po_detail_lot_no         = addslashes($_POST['t_po_detail_lot_no']);
        $t_po_detail_cust           = addslashes($_POST['t_po_detail_cust']);
        $t_po_detail_item           = addslashes($_POST['t_po_detail_item']);
        $t_po_detail_qty            = addslashes($_POST['t_po_detail_qty']);
        $t_po_detail_prod           = addslashes($_POST['t_po_detail_prod']);
        $t_po_detail_prod_date      = addslashes($_POST['t_po_detail_prod_date']);        
        $t_po_detail_delv_date      = addslashes($_POST['t_po_detail_delv_date']);        
        $t_po_detail_prod_weight    = addslashes($_POST['t_po_detail_prod_weight']);
                           
        echo $this->record->detailCreate($t_po_detail_no, $t_po_detail_date, $t_po_detail_lot_no, $t_po_detail_cust,
                                         $t_po_detail_item, $t_po_detail_qty, $t_po_detail_prod, $t_po_detail_prod_date,
                                         $t_po_detail_delv_date, $t_po_detail_prod_weight);
        
    }
    
    function detailUpdate($t_po_detail_lot_no=null)
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $t_po_detail_cust           = addslashes($_POST['t_po_detail_cust']);
        $t_po_detail_item           = addslashes($_POST['t_po_detail_item']);
        $t_po_detail_qty            = addslashes($_POST['t_po_detail_qty']);
        $t_po_detail_prod           = addslashes($_POST['t_po_detail_prod']);
        $t_po_detail_prod_date      = addslashes($_POST['t_po_detail_prod_date']);        
        $t_po_detail_delv_date      = addslashes($_POST['t_po_detail_delv_date']);        
        $t_po_detail_prod_weight    = addslashes($_POST['t_po_detail_prod_weight']);
                           
        echo $this->record->detailUpdate($t_po_detail_lot_no, $t_po_detail_cust, $t_po_detail_item, $t_po_detail_qty, 
                                         $t_po_detail_prod, $t_po_detail_prod_date, $t_po_detail_delv_date,
                                         $t_po_detail_prod_weight);
    }
    
    function detailDelete()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();

        $t_po_detail_lot_no     = addslashes($_POST['t_po_detail_lot_no']);
        
        echo $this->record->detailDelete($t_po_detail_lot_no);
        
    }
        
    function getItem()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        echo $this->record->getItem();
    }
    
    function getCust()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        echo $this->record->getCust();
    }
    
    function calcProdQty()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        $m_item_id = addslashes($_POST['m_item_id']);
        $query = $this->record->calcProdQty($m_item_id);
        foreach ($query->result() as $data)
        {
            echo json_encode(array('success'=>true,'qty'=>$data->m_item_qty_box));
        }
    }
    
    function detailGenerateProd()
    {
        $auth       = new Auth();
        $auth->restrict();
        
        if(!isset($_POST))	
            show_404();
        
        $item_id    = addslashes($_POST['item_id']);
        $prod_qty   = addslashes($_POST['prod_qty']);
        $lot        = addslashes($_POST['lot']);
        
        echo $this->record->detailGenerateProd($item_id, $prod_qty, $lot);

    }
    
    // LOT //
    function lot()
    {                
        $auth   = new Auth();
        $auth->restrict();
        
        if (isset($_GET['grid']))
        {
            echo $this->record->lot($_GET['nilailot']);
        }
        else
        {
            $this->load->view('transaksi/po/v_po');
        }
    }
    
    // PRINT CARD //
    function printAll($id=null)
    {
        $auth = new Auth();

        $auth->restrict();
        //$auth->cek_menu(14);
        
        
        define('FPDF_FONTPATH',$this->config->item('fonts_path'));
        //$id = $this->uri->segment(4);
        //$data['rows'] = $this->record->printAll($id);
        $data = $this->record->printAll($id);
        $this->load->view('transaksi/po/v_po_card_print.php',$data);
    }
    
    function printSublot()
    {
        $auth = new Auth();

        $auth->restrict();
        //$auth->cek_menu(14);
        
        
        define('FPDF_FONTPATH',$this->config->item('fonts_path'));
        //$id = $this->uri->segment(4);
        $data= $this->record->printSublot($_GET['lot'], $_GET['sublot']);
        $this->load->view('transaksi/po/v_po_card_print.php',$data);
    }
    
    function printSelected($id=null)
    {
        $auth = new Auth();

        $auth->restrict();
        //$auth->cek_menu(14);
        
        
        define('FPDF_FONTPATH',$this->config->item('fonts_path'));
        //$id = $this->uri->segment(4);
        $data = $this->record->printSelected($id);
        $this->load->view('transaksi/po/v_po_card_print.php',$data);
    }
}

/* End of file Scan.php */
/* Location: ./application/controllers/Scan.php */