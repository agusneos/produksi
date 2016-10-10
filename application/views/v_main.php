<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html>
<head>   
    <meta charset="UTF-8">
    <title>Input Hasil Produksi</title>
    <link rel="icon" type="image/png" href="<?=base_url('assets/easyui/themes/icons/screw.png')?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/easyui/themes/default/easyui.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/easyui/themes/icon.css')?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/easyui/themes/color.css')?>">
    <script type="text/javascript" src="<?=base_url('assets/easyui/jquery.min.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/easyui/jquery.easyui.min.js')?>"></script>
    
    <script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
    <script type="text/javascript" src="<?=base_url('assets/accounting/accounting.js')?>"></script>

    <script type="text/javascript">
        function startTime() {
            var months  = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var myDays  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum&#39;at', 'Sabtu'];
            var date    = new Date();
            var day     = date.getDate();
            var month   = date.getMonth();
            var thisDay = date.getDay(),
                thisDay = myDays[thisDay];
            var yy      = date.getYear();
            var year    = (yy < 1000) ? yy + 1900 : yy;

            var today   = new Date(),
            curr_hour   = today.getHours(),
            curr_min    = today.getMinutes(),
            curr_sec    = today.getSeconds();
            curr_hour   = checkTime(curr_hour);
            curr_min    = checkTime(curr_min);
            curr_sec    = checkTime(curr_sec);
            $('#clock').linkbutton({text:thisDay + ', ' + day + ' ' + months[month] + ' ' + year + ' - ' + curr_hour+":"+curr_min+":"+curr_sec});
            }
        function checkTime(i) {
            if (i<10) {
                i="0" + i;
            }
            return i;
        }
        setInterval(startTime, 500);
    </script>
    
    <script type="text/javascript">
        function update(){
            var c = $('#isi');
            var p = c.layout('panel','center');
            p.panel({
                href:'<?php echo site_url('scan/viewupdate'); ?>',
                onLoad:function(){
                    $('#afterKg').textbox('disable');
                    $('#scanId').next().find('input').focus();
                    $('#scanId').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            var scId    = $('#scanId').textbox('getValue');
                            $.post('<?php echo site_url('scan/cardCheck'); ?>',{scId:scId},function(result){
                                if (result.success){
                                    $('#afterKg').textbox('enable');
                                    $('#afterKg').next().find('input').focus();
                                    $('#currentKg').textbox('setValue', result.current);
                                    $('#grPcs').textbox('setValue', result.grpcs);
                                    $('#idPros').textbox('setValue', result.prosid);
                                }
                                else{
                                    var win = $.messager.alert('Error','Gagal !'+('<br/>')+'Data Tidak Ditemukan'+('<br/>'),'error', function(){
                                        $('#afterKg').textbox('disable');
                                        $('#scanId').textbox('setValue', '');
                                        $('#currentKg').textbox('setValue', '');
                                        $('#afterKg').textbox('setValue', '');
                                        $('#grPcs').textbox('setValue', '');
                                        $('#idPros').textbox('setValue', '');
                                        $('#scanId').next().find('input').focus();
                                    });
                                    win.window('window').addClass('bg-error');
                                }
                            },'json');
                        }
                    });
                    $('#afterKg').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            var idPros  = $('#idPros').textbox('getValue');
                            var grPcs   = $('#grPcs').textbox('getValue');
                            var afterKg = $('#afterKg').textbox('getValue');
                            $.post('<?php echo site_url('scan/update'); ?>',{idPros:idPros,grPcs:grPcs,afterKg:afterKg},function(result){
                                if (result.success){
                                    $.messager.show({
                                        title   : 'Info',
                                        msg     : '<div class="messager-icon messager-info"></div><div>Data Berhasil Diubah</div>'
                                    });                            
                                }
                                else{
                                    var win = $.messager.alert('Error','Gagal !'+('<br/>')+result.error,'error', function(){
                                        $('#scanId').next().find('input').focus();
                                    });
                                    win.window('window').addClass('bg-error');
                                }
                                $('#scanId').textbox('setValue', '');
                                $('#afterKg').textbox('setValue', '');
                                $('#currentKg').textbox('setValue', '');
                                $('#grPcs').textbox('setValue', '');
                                $('#idPros').textbox('setValue', '');
                                $('#afterKg').textbox('disable');
                                $('#scanId').next().find('input').focus();
                            },'json');
                        }
                    });
                }
            });    
        }
        
        function scan(){
            var c = $('#isi');
            var p = c.layout('panel','center');
            p.panel({
                href:'<?php echo site_url('scan/index'); ?>',
                onLoad:function(){
                    $('#scanId').textbox('disable');
                    $('#shifId').next().find('input').focus();
                    $('#shifId').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            $('#lineId').next().find('input').focus();
                        }
                    });
                    $('#lineId').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            $('#machId').next().find('input').focus();
                        }
                    });
                    $('#machId').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            var namaProses  = '<?php echo strtoupper($this->session->userdata('proc'));?>';
                            var liid        = $('#lineId').textbox('getValue');
                            var mcid        = $('#machId').textbox('getValue');
                            $.post('<?php echo site_url('scan/machCheck'); ?>',{liid:liid,mcid:mcid},function(result){
                                if (result.success){
                                    $('#scanId').textbox('enable');
                                    $('#scanId').next().find('input').focus();
                                     mesin = result.machineId;
                                }
                                else{
                                    var win = $.messager.alert('Error','Gagal !'+('<br/>')+namaProses+('<br/>')+'Line : '+liid+('<br/>')+'Mesin : '+mcid+('<br/>')+'Tidak Ada !','error', function(){
                                        $('#scanId').textbox('disable');
                                        $('#lineId').textbox('setValue', '');
                                        $('#machId').textbox('setValue', '');
                                        $('#lineId').next().find('input').focus();
                                    });
                                    win.window('window').addClass('bg-error');
                                }
                            },'json');
                        }
                    });
                    $('#scanId').textbox('textbox').keypress(function(e){
                        if (e.keyCode == 13){
                            var scid = $('#scanId').textbox('getValue');
                            var shid = $('#shifId').textbox('getValue');
                            $.post('<?php echo site_url('scan/create'); ?>',{scid:scid,shid:shid,mcid:mesin},function(result){
                                if (result.success){
                                    if (result.warning){
                                         var warn = $.messager.alert('Informasi',result.info,'warning', function(){
                                            $('#scanId').next().find('input').focus();
                                            $.messager.show({
                                                title   : 'Info',
                                                msg     : '<div class="messager-icon messager-info"></div><div>Data Berhasil Diinput</div>'
                                            });
                                        });
                                        warn.window('window').addClass('bg-warning');
                                    }
                                    else{
                                        $.messager.show({
                                            title   : 'Info',
                                            msg     : '<div class="messager-icon messager-info"></div><div>Data Berhasil Diinput</div>'
                                        });
                                    }                                    
                                }
                                else{
                                    var win = $.messager.alert('Error','Gagal !'+('<br/>')+result.error,'error', function(){
                                        $('#scanId').next().find('input').focus();
                                    });
                                    win.window('window').addClass('bg-error');
                                }
                                $('#scanId').textbox('setValue', '');
                            },'json');
                        }
                    });
                }
            });    
        }
        
        
    </script>
    
    <style type="text/css">
        .ftitle{
            font-size:14px;
            font-weight:bold;
            padding:5px 0;
            margin-bottom:10px;
            border-bottom:1px solid #ccc;
        }
        .fitem{
            margin-bottom:5px;
        }
        .fitem label{
            display:inline-block;
            width:80px;
        }        
        .fitem input{
            font-size:20px;
        }
        .easyui-numberbox {
            text-align: right;
	}
        .easyui-textbox {
            text-align: center;
	}
        .messager-body{
		font-size: 20px;
	}
        .bg-error{ 
            background: red;
        }
        .bg-error .panel-title{
            color:#fff;
        }
        .bg-warning{ 
            background: yellow;
        }
        .bg-warning .panel-title{
            color:#000;
        }
    </style>
    
</head>
<body>    
    <div class="easyui-layout" fit="true">
        <div data-options="region:'north'" style="height:10%;background-color:#daeef5">
            <h1 align="center">PROSES PRODUKSI <?php echo strtoupper($this->session->userdata('proc'));?></h1>
        </div>
        <div data-options="region:'center'">
            <div id="isi" class="easyui-layout" fit="true">
                <div data-options="region:'north'" style="height:5%">
                    <div class="easyui-layout" fit="true">
                        <div data-options="region:'center'" style="font-size:150%;padding:1px">
                            <?php echo $this->session->userdata('name');?>
                        </div>
                        <div data-options="region:'east'" style="width:20%;padding:1px">
                            <a href="javascript:void(0)" class="easyui-linkbutton" id="clock" data-options="plain:true,iconCls:'icon-time'"></a>
                        </div>
                    </div>
                </div>
                <div data-options="region:'center'">
                    <h2 align="center">CENTER 2</h1>
                </div>
                <div data-options="region:'west'" style="width:5%">
                    <div id="titlebar" style="padding:2px">
                        <a href="javascript:scan()" class="easyui-linkbutton" style="width:100%" data-options="iconCls:'icon-qrcode',size:'large',iconAlign:'top'">SCAN</a>
                        <a href="javascript:update(0)" class="easyui-linkbutton" style="width:100%" data-options="iconCls:'icon-large-smartart',size:'large',iconAlign:'top'">UPDATE</a>
                        <a href="<?php echo site_url('main/logout'); ?>" class="easyui-linkbutton" style="width:100%" data-options="iconCls:'icon-large_logout',size:'large',iconAlign:'top'">LOGOUT</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<!-- End of file v_main.php -->
<!-- Location: ./application/views/v_main.php -->