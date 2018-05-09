<?php $this->load->view('admin/layout/_meta')?>
<title>菜单管理</title>
<script>
    var menuAdd_url = "<?php echo site_url('admin/MenuManage/menuAdd')?>";
</script>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 系統管理 <span class="c-gray en">&gt;</span> 菜单管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="cl pd-5 bg-1 bk-gray"><span class="l">
            <!--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>-->
            <a class="btn btn-primary radius" href="javascript:;" onclick="menu_add('添加菜单按钮',menuAdd_url,'780','450')"><i class="Hui-iconfont">&#xe600;</i> 添加菜单按钮</a> </span></div>
	<table class="table table-border table-bordered table-hover table-bg">
		<thead>
			<tr class="text-c">
				<!--<th width="25"><input type="checkbox" value="" name=""></th>-->
				<th width="100">菜单名</th>
				<th width="100">菜单图标(css id)</th>
				<th width="120">icon图标</th>
				<th width="200">菜单链接地址url</th>
				<th width="80">父级菜单id</th>
				<th width="80">排序</th>
				<th width="100">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($menu_list as $menu):?>
			<tr class="text-c">
				<!--<td><input type="checkbox" value="" name=""></td>-->
				<td><b style="color: red;"><?php echo $menu['title'];?></b></td>
                <td><?php echo $menu['id_name'];?></td>
                <td><?php echo htmlspecialchars($menu['menu_icon']);?></td>
                <td><?php echo $menu['menu_url'];?></td>
                <td><?php echo $menu['p_id'];?></td>
                <td><?php if ($menu['p_id']==0){echo "<b style='color: red'>".$menu['sort']."</b>";}else{$menu['sort'];};?></td>
				<td class="f-14">
                    <a title="编辑" href="javascript:;" onclick="menu_edit('配置编辑','menuEdit?id='+<?php echo $menu['id'];?>,<?php echo $menu['id'];?>)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                    <a title="删除" href="javascript:;" onclick="menu_del(this,'<?php echo $menu['id'];?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                </td>
			</tr>
            <?php if ($menu['subMenu']):?>
                <?php foreach ($menu['subMenu'] as $submenu):?>
                        <tr class="text-c">
                            <!--<td><input type="checkbox" value="" name=""></td>-->
                            <td><?php echo $submenu['title'];?></td>
                            <td><?php echo $submenu['id_name'];?></td>
                            <td><?php echo $submenu['menu_icon'];?></td>
                            <td><?php echo $submenu['menu_url'];?></td>
                            <td><?php echo $submenu['p_id'];?></td>
                            <td><?php echo $submenu['sort'];?></td>
                            <td class="f-14">
                                <a title="编辑" href="javascript:;" onclick="menu_edit('配置编辑','menuEdit?id='+<?php echo $submenu['id'];?>,<?php echo $submenu['id'];?>)" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                <a title="删除" href="javascript:;" onclick="menu_del(this,'<?php echo $submenu['id'];?>')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            </td>
                        </tr>
                <?php endforeach;?>
            <?php endif;?>
            <?php endforeach;?>
		</tbody>
	</table>
    <?php echo $page_show;?>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript">
/*管理员-角色-添加*/
function menu_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-角色-编辑*/
function menu_edit(title,url,id,w,h){
    layer_show(title,url,780,450);
}
/*管理员-角色-删除*/
function menu_del(obj,id){
	layer.confirm('角色删除须谨慎，确认要删除吗？',function(roleId){
		$.ajax({
			type: 'POST',
			url: "<?php echo site_url('admin/MenuManage/delMenu');?>",
            data:{id:id},
            dataType:"JSON",
			success: function(data){
			    if(data.errcode==0){
                    $(obj).parents("tr").remove();
                    layer.msg('删除成功!',{icon:1,time:1000});
                }else{
                    layer.msg('删除失败！'+data.message,{icon:5,time:1500});
                }

			},
		});
	});
}
</script>
</body>
</html>