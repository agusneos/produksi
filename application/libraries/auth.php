<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * Auth library
 *
 * @author  Anggy Trisnawan
 */
class Auth{
    var $CI = NULL;
    function __construct()
    {
        // get CI's object
        $this->CI =& get_instance();
    }
    // untuk validasi login
    function do_login($username,$password)
    {
        // cek di database, ada ga?
        $this->CI->db->from('user');
        $this->CI->db->where('username',$username);
        $this->CI->db->where('password=MD5("'.$password.'")','',false);
        $result = $this->CI->db->get();
        if($result->num_rows() == 0)
        {
           // username dan password tsb tidak ada
            return false;
        }
        else
        {
            // ada, maka ambil informasi dari database
            $userdata = $result->row();
            $session_data = array(
                'id'   => $userdata->id,
                'nama'      => $userdata->name,
                'level'     => $userdata->level
            );
            // buat session
            $this->CI->session->set_userdata($session_data);
            return true;
        }
    }
    
    function init($ip, $id){
        $this->CI->db->select('m_computer_process_cat, m_process_cat_id, m_process_cat_name');
        $this->CI->db->from('m_computer');
        $this->CI->db->join('m_process_cat', 'm_computer_process_cat=m_process_cat_id', 'left');
        $this->CI->db->where('m_computer_ip',$ip);
        $result1 = $this->CI->db->get();
        if($result1->num_rows() == 0){
            return json_encode(array('success'=>'machine not register'));
        }
        else{
            $this->CI->db->select('*');
            $this->CI->db->from('m_operator');
            $this->CI->db->where('m_operator_nik',$id);
            $result2 = $this->CI->db->get();
            if($result2->num_rows() == 0){
                return json_encode(array('success'=>'user not register'));
            }
            else{
                $userdata1 = $result1->row();
                $userdata2 = $result2->row();
                $session_data = array(
                    'procid'    => $userdata1->m_process_cat_id,
                    'proc'      => $userdata1->m_process_cat_name,
                    'nik'       => $userdata2->m_operator_nik,
                    'name'      => $userdata2->m_operator_name,
                    'auth'      => $userdata2->m_operator_auth
                );
                $this->CI->session->set_userdata($session_data);
                return json_encode(array('success'=>true));
              /*  return json_encode(array('success'=>true,
                                         'proc'     => $this->CI->session->userdata('proc'),
                                         'nik'      => $this->CI->session->userdata('nik'),
                                         'name'     => $this->CI->session->userdata('name'),
                                         'auth'     => $this->CI->session->userdata('auth'),)); */
            }
            
        }
    }
    
    // untuk mengecek apakah user sudah login/belum
    function is_logged_in()
    {
        if($this->CI->session->userdata('id') == '')
        {
            return false;
        }
        return true;
                
    }
    
    function is_logged_in_nik()
    {
        if($this->CI->session->userdata('nik') == '')
        {
            return false;
        }
        return true;
                
    }
    // untuk validasi di setiap halaman yang mengharuskan authentikasi
    function restrict()
    {
        if($this->is_logged_in_nik() == false)
        {
            redirect('');//redirect ke home
        }
    }
    // untuk mengecek menu
    function cek_menu($idmenu)
    {
        $this->CI->load->model('usermodel');
        $status_user = $this->CI->session->userdata('level');
        $allowed_level = $this->CI->usermodel->get_array_menu($idmenu);
        if(in_array($status_user,$allowed_level) == false)
        {
            die("Maaf, Anda tidak berhak untuk mengakses halaman ini.");
        }
    }
   // untuk logout
    function do_logout()
    {
        $this->CI->session->sess_destroy();
    }

}
