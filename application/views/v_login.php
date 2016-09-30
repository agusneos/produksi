<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<html>
    
<meta charset="UTF-8">    
<title>Login</title>    
<link rel="icon" type="image/png" href="<?=base_url('assets/easyui/themes/icons/login.png')?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/easyui/themes/default/easyui.css')?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/easyui/themes/icon.css')?>">
<script type="text/javascript" src="<?=base_url('assets/easyui/jquery.min.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/jquery.easyui.min.js')?>"></script>
<div data-options="region:'center'">
    <div class="easyui-panel" title="Login to system" data-options="style:{margin:'15% auto'}" style="width:400px;padding:30px 70px 20px 70px">
        <form id="form-login" method="post" novalidate onsubmit="return false">
            <div style="margin-bottom:20px">
                <input id="password" name="password" class="easyui-textbox" type="password" style="width:100%;height:40px;padding:12px" data-options="prompt:'ID Operator',iconWidth:38" tabindex="1">
            </div>
        </form>
    </div>

</div>

<script type="text/javascript">
    $(function(){	
        $('#password').next().find('input').focus();
    });
    
    function login(){
        var ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
        var id = $('#password').passwordbox('getValue');
        $.post('<?php echo site_url('main/init'); ?>',{ip:ip, id:id},function(result){            
            if (result.success == 'machine not register'){
                $('#form-login').form('clear');
                $.messager.show({
                    title   : 'Error',
                    msg     : '<div class="messager-icon messager-error"></div><div>Komputer Belum Diregister !</div>',
                    showType: 'fade',
                    timeout : 1000,
                    height  : 120, 
                    style   : {
                                right:'',
                                bottom:''
                    }
                });
            }
            
            else if(result.success == 'user not register'){
                $('#form-login').form('clear');
                $.messager.show({
                    title   : 'Error',
                    msg     : '<div class="messager-icon messager-error"></div><div>Operator Belum Diregister !</div>',
                    showType: 'fade',
                    timeout : 1000,
                    height  : 120,
                    style   : {
                                right:'',
                                bottom:''
                    }
                });
            }
            else{
       /*         $.messager.show({
                    title   : 'Info',
                    msg     : '<div class="messager-icon messager-info"></div><div>Inisialisasi Berhasil !</div>',
                    style   : {
                                right:'',
                                bottom:''
                    }
                }); */
                //alert(result.proc+' / '+result.nik+' / '+result.name+' / '+result.auth);
                progress();
            }
        },'json');
    }
    
    function progress(){
        $.messager.progress({
            title:'Please wait',
            msg:'Loading data...'
        });
        setTimeout(function(){
            $.messager.progress('close');
             window.location.assign('<?php echo site_url("")//redirect ke index; ?>');
        },1000);           
    }
    
    $(function(){    
        $('#password').textbox('textbox').keypress(function(e){
            if (e.keyCode == 13){
                login();
            }
        });
    });
       

</script>
 
</html>

<!-- End of file v_login.php -->
<!-- Location: ./application/views/v_login.php -->