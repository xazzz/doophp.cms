<? if(!defined('LUA_ROOT')) exit('Access Denied'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>网站内容管理系统 - DooPHP.Net</title>
<link href="<? echo $this->img; ?>css/Lua.css" rel="stylesheet" />
<script src="<? echo $this->img; ?>js/jQuery1.7.2.js" type="text/javascript"></script>
</head>
<body id="indexid">
	<div id="cmsbox" style="width:100%;">
		<div id="top"> 
			<div class="floatr">
				<div class="top-r-box">
					<div class="top-right-boxr">
						<div class="top-r-t">
							您好 <a class='tui' style="text-decoration:underline;"><? echo $this->user['username']; ?></a><span>-</span><a target="_top" href="./logout.htm" id="outhome" title="退出" class='tui'>退出</a>
							<div><a href="javascript:;" style="color: rgb(242, 251, 2); ">大繁若简、浓妆淡抹之间灵活转换</a></div>
						</div>
						<div class="langs">
							<div class="langtxt"><div class="langkkkbox" style="color:white;">系统版本: DooPHP.Cms <?=LUA_VER?></div></div>
						</div>
					</div>
					<div class="nav">
						<ul id="topnav">
							<? if($this->user['perm'] == SUPER_MAN) { ?>
								<? if(SYSNAME == ADMIN_ROOT) { ?>
								<li class="list"><a href="/<?=ADMIN_ROOT?>/" class="onnav"><span class="c1"></span>系统设置</a></li>
								<? } else { ?>
								<li class="list"><a href="/<?=ADMIN_ROOT?>/"><span class="c1"></span>系统设置</a></li>
								<? } if(is_array($list)) { foreach($list as $v) { ?>									<? if(SYSNAME == $v['path']) { ?>
									<li class="list"><a href="/<?=$v['path']?>/<?=ADMIN_ROOT?>/" class="onnav"><span class="<?=$v['classname']?>"></span><?=$v['name']?></a></li>
									<? } else { ?>
									<li class="list"><a href="/<?=$v['path']?>/<?=ADMIN_ROOT?>/"><span class="<?=$v['classname']?>"></span><?=$v['name']?></a></li>
									<? } ?>
								<? } } } else { if(is_array($list)) { foreach($list as $v) { ?>									<? if($this->user['perm'] == $v['groupname']) { ?>
									<li class="list"><a href="/<?=$v['path']?>/<?=ADMIN_ROOT?>/" class="onnav"><span class="<?=$v['classname']?>"></span><?=$v['name']?></a></li>
									<? } ?>
								<? } } } ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="floatl">
				<a href="http://www.doophp.net" target="_blank"><img src="<? echo $this->img; ?>img/logo.gif" /></a>
			</div>
		</div>
		<div id="cmsbox" style="width:100%;">
			<div id="content">
				<div class="floatr" id="right">
					<div class="iframe">
						<div class="min"><iframe frameborder="0" id="main" name="main" src="./info.htm" scrolling="no"></iframe></div>
					</div>
				</div>
				<div class="floatl" id="left">
					<div class="fast">
						<a target="_blank" href="<? if(SYSNAME == ADMIN_ROOT) { ?>/<? } else { ?>/<?=SYSNAME?>/<? } ?>" title="网站首页">网站首页</a>
						<span></span>
						<a href="./" title="后台首页" target="_top">后台首页</a>
					</div>
					<div class="nav_list" id="leftnav">
						<ul>
							<li><a href="./info.htm" target="main" class="on">系统信息</a></li>
							<? if(SYSNAME == ADMIN_ROOT) { ?>
							<li><a href="./admin.htm" target="main">管理员管理</a></li>
							<li><a href="./channel.htm" target="main">频道管理</a></li>
							<li><a href="./model.htm" target="main">模型管理</a></li>
							<li><a href="./plugin.htm" target="main">插件管理</a></li>
							<? } else { ?>
							<li><a href="./member.htm" target="main">会员管理</a></li>
							<li><a href="./category.htm" target="main">栏目管理</a></li>
							<li><a href="./piece.htm" target="main">碎片管理</a></li>
							<li><a href="./plugin.htm" target="main">插件管理</a></li>
							<? } ?>
						</ul>
					</div>
					<div class="claer"></div>
					<div class="left_footer">感谢使用 <a href="http://www.doophp.net/" target="_blank">DooPHP</a></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<script>
		function dheight(){
			var m=$("#main").contents().find("body").height();
			if (m<580){
				m = 580;
			}
			$('#main').height(m);
			$('#leftnav').height(m-70);
		}
		$("#main").load(function(){
			dheight();
		})
		$(function(){
			var nav = $('#leftnav li a');
			nav.click(function(){
				var self = $(this);
				nav.removeClass('on');
				self.addClass('on');
			});
		});
	</script>
</body>
</html>