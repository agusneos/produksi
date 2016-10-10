<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<html>
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
            text-align: center;
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
        .textbox .textbox-text,
        .textbox .textbox-prompt{
                font-size: 20px;
        }
    </style>
<div data-options="region:'center'">
    <div class="easyui-panel" title="UPDATE" data-options="style:{margin:'15% auto'}" style="width:400px;padding:30px 30px 20px 80px">
        <form id="form-scan" method="post" novalidate onsubmit="return false">
            <div style="font-size:20px;margin-bottom:20px">
                <input id="scanId" name="scanId" class="easyui-numberbox" label="KARTU" labelPosition="left" style="width:80%;height:40px;padding:12px" tabindex="1">
            </div>
            <div style="font-size:20px;margin-bottom:20px">
                <input id="idPros" name="idPros" class="easyui-numberbox" label="Proses Id" labelPosition="left" data-options="min:0,precision:0" style="width:80%;height:40px;padding:12px" readonly>
            </div>
            <div style="font-size:20px;margin-bottom:20px">
                <input id="currentKg" name="currentKg" class="easyui-numberbox" label="Before" labelPosition="left" data-options="min:0,precision:2" style="width:80%;height:40px;padding:12px" readonly><a> Kg</a>
            </div>
            <div style="font-size:20px;margin-bottom:20px">
                <input id="grPcs" name="grPcs" class="easyui-numberbox" label="Gr/Pcs" labelPosition="left" data-options="min:0,precision:2" style="width:80%;height:40px;padding:12px" readonly><a> Gr</a>
            </div>
            <div style="font-size:20px;margin-bottom:20px">
                <input id="afterKg" name="afterKg" class="easyui-numberbox" label="After" labelPosition="left" data-options="min:0,precision:2" style="width:80%;height:40px;padding:12px" tabindex="2"><a> Kg</a>
            </div>
        </form>
    </div>

</div>

<script type="text/javascript">

</script>
 
</html>

<!-- End of file v_update.php -->
<!-- Location: ./application/views/v_update.php -->