<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_scan extends CI_Model
{    
    static $table1  = 't_process';
    static $table2  = 't_prod';
    static $table3  = 't_po_detail';
    static $table4  = 'm_process';
    static $table5  = 'm_machine';
    static $table6  = '';
    static $table7  = '';
    static $table8  = '';
    static $table9  = '';

    public function __construct() {
        parent::__construct();
        //$this->load->helper('database'); // Digunakan untuk memunculkan data Enum
    }
    
    function round_up ( $value, $precision ) { 
        $pow = pow ( 10, $precision ); 
        return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
    }

    function create($id, $procid, $procName, $nik, $shid, $mcid){        
        $this->db->select('t_prod_qty, m_process_seq, t_prod_qty');
        $this->db->join(self::$table3, 't_prod_lot = t_po_detail_lot_no', 'left')
                 ->join(self::$table4, 't_po_detail_item = m_process_id', 'left');
        $this->db->where('t_prod_id', $id)
                 ->where('m_process_proc_cat_id', $procid);
        $query_1    = $this->db->get(self::$table2);
        $row_1      = $query_1->row();
        if($row_1){     // Memeriksa apakah item tsb mempunyai proses yang akan diinput + sequence
            $this->db->select('t_process_cat, t_po_detail_item, t_process_qty');
            $this->db->join(self::$table2, 't_process_prod_id = t_prod_id', 'left')
                     ->join(self::$table3, 't_prod_lot = t_po_detail_lot_no', 'left');
            $this->db->where('t_process_prod_id', $id);
            $this->db->order_by('t_process_id', 'desc');
            $this->db->limit(1);
            $query_3    = $this->db->get(self::$table1);
            $row_3      = $query_3->row();
            if($row_3){      // memeriksa apakah sudah pernah masuk card tsb di tabel proses
                $this->db->select('m_process_seq, m_process_weight');
                $this->db->where('m_process_id', $row_3->t_po_detail_item)
                         ->where('m_process_proc_cat_id', $row_3->t_process_cat);
                $query_4    = $this->db->get(self::$table4);
                $row_4      = $query_4->row();
                $lastProcess = ($row_4->m_process_seq)+1;
                $nextProcess = $row_1->m_process_seq;
                if($lastProcess == $nextProcess){   // Memeriksa apakah urutan prosesnya benar ?
                    $stdQty     = $row_1->t_prod_qty;
                    $lastQty    = $row_3->t_process_qty;
                    if($stdQty <> $lastQty){    // memeriksa apakah qty proses terakhir masih std per kartu?
                        $berat      = $this->round_up(($row_4->m_process_weight*$lastQty)/1000,2);
                        $warning    = TRUE;
                        $info       = 'Standard Qty sudah berubah dari proses sebelumnya. Harap sesuaikan beratnya menjadi '.$berat.' Kg';
                    }
                    else{
                        $warning    = FALSE;
                        $info       = '';
                    }
                    $query = $this->db->insert(self::$table1,array(
                        't_process_proc_seq'        => $nextProcess,
                        't_process_cat'             => $procid,
                        't_process_prod_id'         => $id,
                        't_process_qty'             => $lastQty,
                        't_process_operator_nik'    => $nik,
                        't_process_shif'            => $shid,
                        't_process_machine'         => $mcid
                    ));
                    if($query){
                        return json_encode(array('success'=>true,'warning'=>$warning,'info'=>$info));
                    }
                    else{
                        return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
                    }
                }
                else if($lastProcess < $nextProcess){
                    return json_encode(array('success'=>false,'error'=>'Proses Sebelumnya Terlewati'));
                }
                else{
                    return json_encode(array('success'=>false,'error'=>'Proses Sudah pernah diinput'));
                }
            }
            else{
                if($row_1->m_process_seq==1){
                    $query = $this->db->insert(self::$table1,array(
                        't_process_proc_seq'        => 1,
                        't_process_cat'             => $procid,
                        't_process_prod_id'         => $id,
                        't_process_qty'             => $row_1->t_prod_qty,
                        't_process_operator_nik'    => $nik,
                        't_process_shif'            => $shid,
                        't_process_machine'         => $mcid
                    ));
                    if($query){
                        return json_encode(array('success'=>true));
                    }
                    else{
                        return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
                    }
                }
                else{
                    return json_encode(array('success'=>false,'error'=>'Proses Awal Belum Diinput'));
                }
            }
        }
        else{
            return json_encode(array('success'=>false,'error'=>'Proses '.$procName.' Tidak ada untuk item tersebut'));
        }
    }
    
    function machCheck($procid, $liid, $mcid){
        $this->db->select('m_machine_id');
        $this->db->where('m_machine_lines', $liid)
                 ->where('m_machine_mac', $mcid)
                 ->where('m_machine_proc', $procid);
        $query_1    = $this->db->get(self::$table5);
        $row_1      = $query_1->row();
        if($row_1){
            return json_encode(array('success'=>true,'machineId'=>$row_1->m_machine_id));
        }
        else{
            return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
        }
    }
    
    function exportExcel(){
        
    }
    ////////////////////////////////
    function update($t_po_header_no_old, $t_po_header_no, $t_po_header_cust, $t_po_header_date)
    {
        $this->db->where('t_po_header_no', $t_po_header_no_old);
        $query = $this->db->update(self::$table1,array(
            't_po_header_no'        => $t_po_header_no,
            't_po_header_cust'      => $t_po_header_cust,
            't_po_header_date'      => $t_po_header_date
        ));
        if($query)
        {
            return json_encode(array('success'=>true));
        }
        else
        {
            return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
        }
    }
    
    function delete($t_po_header_no)
    {
        $query = $this->db->delete(self::$table1, array('t_po_header_no' => $t_po_header_no));
        if($query)
        {
            return json_encode(array('success'=>true));
        }
        else
        {
            return json_encode(array('success'=>false,'error'=>'Data Header tidak bisa dihapus apabila masih ada Data Detail'));
        }
    }
    
    //--DETAIL--//
    
    function detailIndex($id=null)
    {
        $page   = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 50;
        $offset = ($page-1)*$rows;      
        $sort   = isset($_POST['sort']) ? strval($_POST['sort']) : 't_po_detail_lot_no';
        $order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        
        $filterRules = isset($_POST['filterRules']) ? ($_POST['filterRules']) : '';
	$cond = '1=1';
	if (!empty($filterRules)){
            $filterRules = json_decode($filterRules);
            //print_r ($filterRules);
            foreach($filterRules as $rule){
                $rule = get_object_vars($rule);
                $field = $rule['field'];
                $op = $rule['op'];
                $value = $rule['value'];
                if (!empty($value)){
                    if ($op == 'contains'){
                        $cond .= " and ($field like '%$value%')";
                    } else if ($op == 'beginwith'){
                        $cond .= " and ($field like '$value%')";
                    } else if ($op == 'endwith'){
                        $cond .= " and ($field like '%$value')";
                    } else if ($op == 'equal'){
                        $cond .= " and $field = $value";
                    } else if ($op == 'notequal'){
                        $cond .= " and $field != $value";
                    } else if ($op == 'less'){
                        $cond .= " and $field < $value";
                    } else if ($op == 'lessorequal'){
                        $cond .= " and $field <= $value";
                    } else if ($op == 'greater'){
                        $cond .= " and $field > $value";
                    } else if ($op == 'greaterorequal'){
                        $cond .= " and $field >= $value";
                    } 
                }
            }
	}
        
        $this->db->select('t_po_detail_item, m_item_name, t_po_detail_cust, m_cust_name, t_po_detail_qty,
                           t_po_detail_prod, t_po_detail_lot_no, t_po_detail_prod_date, t_po_detail_delv_date,
                           t_po_detail_prod_weight', NULL);
        $this->db->join(self::$table3, 't_po_detail_item=m_item_id', 'left')
                 ->join(self::$table4, 't_po_detail_cust=m_cust_id', 'left');
        $this->db->where($cond, NULL, FALSE)
                 ->where('t_po_detail_no', $id);
        $total  = $this->db->count_all_results(self::$table2);
        
        $this->db->select('t_po_detail_item, m_item_name, t_po_detail_cust, m_cust_name, t_po_detail_qty,
                           t_po_detail_prod, t_po_detail_lot_no, t_po_detail_prod_date, t_po_detail_delv_date,
                           t_po_detail_prod_weight', NULL);
        $this->db->join(self::$table3, 't_po_detail_item=m_item_id', 'left')
                 ->join(self::$table4, 't_po_detail_cust=m_cust_id', 'left');
        $this->db->where($cond, NULL, FALSE)
                 ->where('t_po_detail_no', $id);
        $this->db->order_by($sort, $order);
        $this->db->limit($rows, $offset);
        $query  = $this->db->get(self::$table2);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }
 
        $result = array();
	$result["total"] = $total;
	$result['rows'] = $data;
        
        return json_encode($result);          
    }
    
    function detailCreate($t_po_detail_no, $t_po_detail_date, $t_po_detail_lot_no, $t_po_detail_cust,
                          $t_po_detail_item, $t_po_detail_qty, $t_po_detail_prod, $t_po_detail_prod_date,
                          $t_po_detail_delv_date, $t_po_detail_prod_weight)
    {
        $query = $this->db->insert(self::$table2,array(
            't_po_detail_no'           => $t_po_detail_no,
            't_po_detail_date'         => $t_po_detail_date,
            't_po_detail_lot_no'       => $t_po_detail_lot_no,
            't_po_detail_cust'         => $t_po_detail_cust,
            't_po_detail_item'         => $t_po_detail_item,
            't_po_detail_qty'          => $t_po_detail_qty,
            't_po_detail_prod'         => $t_po_detail_prod,
            't_po_detail_prod_date'    => $t_po_detail_prod_date,
            't_po_detail_delv_date'    => $t_po_detail_delv_date,
            't_po_detail_prod_weight'  => $t_po_detail_prod_weight
        ));
        if($query)
        {
            return json_encode(array('success'=>true));
        }
        else
        {
            return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
        }       
    }
    
    function detailUpdate($t_po_detail_lot_no, $t_po_detail_cust, $t_po_detail_item, $t_po_detail_qty, 
                          $t_po_detail_prod, $t_po_detail_prod_date, $t_po_detail_delv_date,
                          $t_po_detail_prod_weight)
    {
        $this->db->where('t_po_detail_lot_no', $t_po_detail_lot_no);
        $query = $this->db->update(self::$table2,array(
            't_po_detail_cust'         => $t_po_detail_cust,
            't_po_detail_item'         => $t_po_detail_item,
            't_po_detail_qty'          => $t_po_detail_qty,
            't_po_detail_prod'         => $t_po_detail_prod,
            't_po_detail_prod_date'    => $t_po_detail_prod_date,
            't_po_detail_delv_date'    => $t_po_detail_delv_date,
            't_po_detail_prod_weight'  => $t_po_detail_prod_weight
        ));
        if($query)
        {
            return json_encode(array('success'=>true));
        }
        else
        {
            return json_encode(array('success'=>false,'error'=>$this->db->_error_message()));
        }
    }
    
    function detailDelete($t_po_detail_lot_no)
    {
        $query = $this->db->delete(self::$table2, array('t_po_detail_lot_no' => $t_po_detail_lot_no));
        if($query)
        {
            return json_encode(array('success'=>true));
        }
        else
        {
            return json_encode(array('success'=>false,'error'=>'Data tidak bisa dihapus apabila produksi sudah dibuat'));
        }
    }
    
    function getItem()
    {
        $this->db->select('m_item_id, m_item_name', NULL);
        $query  = $this->db->get(self::$table3);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }
    
    function getCust()
    {
        $this->db->select('m_cust_id, m_cust_name', NULL);
        $query  = $this->db->get(self::$table4);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }       
        return json_encode($data);
    }
    
    function calcProdQty($m_item_id)
    {
        $this->db->select('m_item_qty_box');
        $this->db->where('m_item_id', $m_item_id);
        return $this->db->get(self::$table3);
    }
    
    function detailGenerateProd($item_id, $prod_qty, $lot)
    {
        $this->db->select('m_item_qty_box');
        $this->db->where('m_item_id', $item_id);
        $querya  = $this->db->get(self::$table3);
        $rowa = $querya->row();
        
        $this->db->where('t_prod_lot', $lot);
        $this->db->from(self::$table5);
        $queryb  = $this->db->count_all_results();
        
        if ($rowa->m_item_qty_box<1)
        {
            return json_encode(array('success'=>false,'error'=>'Qty/Box belum diisi'));
        }
        else if ($queryb>0)
        {
            return json_encode(array('success'=>false,'error'=>'Produksi Sudah Dibuat'));
        }
        else
        {
            $query = $this->db->simple_query('SELECT GenerateProdCardNew('.$item_id.','.$prod_qty.','.'"'.$lot.'"'.')');
            if($query)
            {
                return json_encode(array('success'=>true));
            }
            else
            {
                return json_encode(array('success'=>false,'error'=>'Qty/Box belum diisi Atau Id Maximum'));
            }
        }
    }
    
    // LOT //
    function lot($lot_id=null)
    {
        $page   = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows   = isset($_POST['rows']) ? intval($_POST['rows']) : 50;
        $offset = ($page-1)*$rows;      
        $sort   = isset($_POST['sort']) ? strval($_POST['sort']) : 't_prod_id';
        $order  = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
        
        $filterRules = isset($_POST['filterRules']) ? ($_POST['filterRules']) : '';
	$cond = '1=1';
	if (!empty($filterRules)){
            $filterRules = json_decode($filterRules);
            //print_r ($filterRules);
            foreach($filterRules as $rule){
                $rule = get_object_vars($rule);
                $field = $rule['field'];
                $op = $rule['op'];
                $value = $rule['value'];
                if (!empty($value)){
                    if ($op == 'contains'){
                        $cond .= " and ($field like '%$value%')";
                    } else if ($op == 'beginwith'){
                        $cond .= " and ($field like '$value%')";
                    } else if ($op == 'endwith'){
                        $cond .= " and ($field like '%$value')";
                    } else if ($op == 'equal'){
                        $cond .= " and $field = $value";
                    } else if ($op == 'notequal'){
                        $cond .= " and $field != $value";
                    } else if ($op == 'less'){
                        $cond .= " and $field < $value";
                    } else if ($op == 'lessorequal'){
                        $cond .= " and $field <= $value";
                    } else if ($op == 'greater'){
                        $cond .= " and $field > $value";
                    } else if ($op == 'greaterorequal'){
                        $cond .= " and $field >= $value";
                    } 
                }
            }
	}
        
        $this->db->where($cond, NULL, FALSE)
                 ->where('t_prod_lot', $lot_id);
        $total  = $this->db->count_all_results(self::$table5);
        
        $this->db->where($cond, NULL, FALSE)
                 ->where('t_prod_lot', $lot_id);
        $this->db->order_by($sort, $order);
        $this->db->limit($rows, $offset);
        $query  = $this->db->get(self::$table5);
                   
        $data = array();
        foreach ( $query->result() as $row )
        {
            array_push($data, $row); 
        }
 
        $result = array();
	$result["total"] = $total;
	$result['rows'] = $data;
        
        return json_encode($result);
    }
    
    // PRINT CARD //
    function printAll($id)
    {
        $sql = 'SELECT t_prod_id, t_po_header_date, t_po_detail_qty, t_po_detail_no, t_po_detail_prod,
                t_po_detail_cust, t.t_prod_lot AS t_prod_lot, t.t_prod_sublot AS t_prod_sublot, t_prod_card, m_item_name, m_item_baking,
                m_item_bom_name, t_po_detail_prod, m_bom_qty, t_po_detail_prod_date, t_prod_qty,
                t_po_detail_delv_date, m_item_mark, m_item_note, t_prod_card_cnt, t_prod_qty_sum
                FROM '.self::$table5.' t
                LEFT JOIN '.self::$table2.' ON t.t_prod_lot=t_po_detail_lot_no
                LEFT JOIN '.self::$table3.' ON t_po_detail_item=m_item_id
                LEFT JOIN '.self::$table1.' ON t_po_detail_no=t_po_header_no
                LEFT JOIN '.self::$table6.' ON m_item_id=m_bom_id
                LEFT JOIN '.self::$table7.' ON m_bom_item=m_item_bom_id
                LEFT JOIN ( SELECT t_prod_lot, t_prod_sublot, COUNT(t_prod_card) t_prod_card_cnt,
                            SUM(t_prod_qty) t_prod_qty_sum
                            FROM t_prod
                            GROUP by t_prod_lot, t_prod_sublot ) t2 
                            ON t.t_prod_lot = t2.t_prod_lot and t.t_prod_sublot = t2.t_prod_sublot
                WHERE t.t_prod_lot="'.$id.'" AND m_item_bom_cat="WIRE" ';
        $header = $this->db->query($sql);
       // $this->db->select('t_po_header.*, t_po_detail.*, m_item.*, t_prod.*, m_bom.*, m_item_bom.*');
       /* $this->db->join(self::$table2, 't_prod_lot=t_po_detail_lot_no', 'left')
                 ->join(self::$table3, 't_po_detail_item=m_item_id', 'left')
                 ->join(self::$table1, 't_po_detail_no=t_po_header_no', 'left')
                 ->join(self::$table6, 'm_item_id=m_bom_id', 'left')
                 ->join(self::$table7, 'm_bom_item=m_item_bom_id', 'left')
                 ->join(self::$table5, 'select t_prod_lot, t_prod_sublot, count(t_prod_card) t_prod_card_cnt, sum(t_prod_qty) t_prod_qty_sum');
        $this->db->where('t_prod_lot', $id)
                 ->where('m_item_bom_cat', 'WIRE');
        $header = $this->db->get(self::$table5);*/
        
        ///// process
        $this->db->select('m_process_cat_name, m_process_weight, t_prod_qty');
        $this->db->join(self::$table9, 'm_process_proc_cat_id=m_process_cat_id', 'left')
                 ->join(self::$table2, 'm_process_id=t_po_detail_item', 'left')
                 ->join(self::$table5, 't_po_detail_lot_no=t_prod_lot', 'left');
        $this->db->where('t_po_detail_lot_no', $id);
        $this->db->group_by('m_process_proc_cat_id');
        $this->db->order_by('m_process_seq', 'ASC');
        $detail = $this->db->get(self::$table8);
        
        $result = array();
	$result['rows'] = $header;
	$result['detail'] = $detail;
        
        return $result;
    }
    
    function printSublot($lot, $sublot)
    {
        $sql = 'SELECT t_prod_id, t_po_header_date, t_po_detail_qty, t_po_detail_no, t_po_detail_prod,
                t_po_detail_cust, t.t_prod_lot AS t_prod_lot, t.t_prod_sublot AS t_prod_sublot, t_prod_card, m_item_name, m_item_baking,
                m_item_bom_name, t_po_detail_prod, m_bom_qty, t_po_detail_prod_date, t_prod_qty,
                t_po_detail_delv_date, m_item_mark, m_item_note, t_prod_card_cnt, t_prod_qty_sum
                FROM '.self::$table5.' t
                LEFT JOIN '.self::$table2.' ON t.t_prod_lot=t_po_detail_lot_no
                LEFT JOIN '.self::$table3.' ON t_po_detail_item=m_item_id
                LEFT JOIN '.self::$table1.' ON t_po_detail_no=t_po_header_no
                LEFT JOIN '.self::$table6.' ON m_item_id=m_bom_id
                LEFT JOIN '.self::$table7.' ON m_bom_item=m_item_bom_id
                LEFT JOIN ( SELECT t_prod_lot, t_prod_sublot, COUNT(t_prod_card) t_prod_card_cnt,
                            SUM(t_prod_qty) t_prod_qty_sum
                            FROM t_prod
                            GROUP by t_prod_lot, t_prod_sublot ) t2 
                            ON t.t_prod_lot = t2.t_prod_lot and t.t_prod_sublot = t2.t_prod_sublot
                WHERE t.t_prod_lot="'.$lot.'" AND t.t_prod_sublot="'.$sublot.'" AND m_item_bom_cat="WIRE" ';
        $header = $this->db->query($sql);
        
        ///// process
        $this->db->select('m_process_cat_name, m_process_weight, t_prod_qty');
        $this->db->join(self::$table9, 'm_process_proc_cat_id=m_process_cat_id', 'left')
                 ->join(self::$table2, 'm_process_id=t_po_detail_item', 'left')
                 ->join(self::$table5, 't_po_detail_lot_no=t_prod_lot', 'left');
        $this->db->where('t_po_detail_lot_no', $lot);
        $this->db->group_by('m_process_proc_cat_id');
        $this->db->order_by('m_process_seq', 'ASC');
        $detail = $this->db->get(self::$table8);
        
        $result = array();
	$result['rows'] = $header;
	$result['detail'] = $detail;
        
        return $result;
    }
    
    function printSelected($id)
    {
        $sql = 'SELECT t_prod_id, t_po_header_date, t_po_detail_qty, t_po_detail_no, t_po_detail_prod,
                t_po_detail_cust, t.t_prod_lot AS t_prod_lot, t.t_prod_sublot AS t_prod_sublot, t_prod_card, m_item_name, m_item_baking,
                m_item_bom_name, t_po_detail_prod, m_bom_qty, t_po_detail_prod_date, t_prod_qty,
                t_po_detail_delv_date, m_item_mark, m_item_note, t_prod_card_cnt, t_prod_qty_sum
                FROM '.self::$table5.' t
                LEFT JOIN '.self::$table2.' ON t.t_prod_lot=t_po_detail_lot_no
                LEFT JOIN '.self::$table3.' ON t_po_detail_item=m_item_id
                LEFT JOIN '.self::$table1.' ON t_po_detail_no=t_po_header_no
                LEFT JOIN '.self::$table6.' ON m_item_id=m_bom_id
                LEFT JOIN '.self::$table7.' ON m_bom_item=m_item_bom_id
                LEFT JOIN ( SELECT t_prod_lot, t_prod_sublot, COUNT(t_prod_card) t_prod_card_cnt,
                            SUM(t_prod_qty) t_prod_qty_sum
                            FROM t_prod
                            GROUP by t_prod_lot, t_prod_sublot ) t2 
                            ON t.t_prod_lot = t2.t_prod_lot and t.t_prod_sublot = t2.t_prod_sublot
                WHERE t.t_prod_id="'.$id.'" AND m_item_bom_cat="WIRE" ';
        $header = $this->db->query($sql);
        
        ///// process
        $this->db->select('m_process_cat_name, m_process_weight, t_prod_qty');
        $this->db->join(self::$table9, 'm_process_proc_cat_id=m_process_cat_id', 'left')
                 ->join(self::$table2, 'm_process_id=t_po_detail_item', 'left')
                 ->join(self::$table5, 't_po_detail_lot_no=t_prod_lot', 'left');
        $this->db->where('t_prod_id', $id);
        $this->db->group_by('m_process_proc_cat_id');
        $this->db->order_by('m_process_seq', 'ASC');
        $detail = $this->db->get(self::$table8);
        
        $result = array();
	$result['rows'] = $header;
	$result['detail'] = $detail;
        
        return $result;
    }
}

/* End of file m_po.php */
/* Location: ./application/models/transaksi/m_po.php */