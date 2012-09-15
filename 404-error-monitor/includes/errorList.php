<?php 
/*
 *     This file is part of 404 Error Monitor.
 *     
 *     404 Error Monitor is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *     
 *     404 Error Monitor is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *    
 *     You should have received a copy of the GNU General Public License
 *     along with 404 Error Monitor.  If not, see <http://www.gnu.org/licenses/gpl-howto.html>.
 */
?>
<?php 
	if (!function_exists('is_admin') || !is_admin()) {
	    header('Status: 403 Forbidden');
	    header('HTTP/1.1 403 Forbidden');
	    exit();
	}
	
	if(function_exists('is_network_admin') && is_network_admin()){
		if (!function_exists('is_super_admin') || !is_super_admin()) {
		    header('Status: 403 Forbidden');
		    header('HTTP/1.1 403 Forbidden');
		    exit();
		} 
	}
	
	
	$error = new errorMonitor_Error();

	if(!is_network_admin()){
		$blog_id = get_current_blog_id();
	} else {
		$blog_id = null;
	}
	
?>
<div class="wrap">  
	<div class="icon32" id="icon-edit"><br></div><h2>404 Error Monitor - Error list</h2>
	<div class="error_monitor-message"></div>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets-wrap">
			
			<div class="metabox-holder" id="dashboard-widgets">


		

			




			    <?php if(
			    	$blog_id &&
					errorMonitor_DataTools::isNetworkInstall() &&
					function_exists('network_admin_url') &&
					function_exists('is_multisite') &&
					function_exists('is_super_admin') &&
					is_multisite()&&
					is_super_admin()):?>
			
					<div style="width:79%;" class="postbox-container">
						<div class="meta-box-sortables ui-sortable" style="min-height: 120px;" id="normal-sortables">
							<div class="postbox" id="network_dashboard_right_now">
								<h3 class="hndle"><span>Options</span></h3>
								<div class="inside">
									<ul>
										<li><a href="<?php echo network_admin_url();?>admin.php?page=errorMotinor">Network admin error list</a></li>
										<li><a href="<?php echo network_admin_url();?>admin.php?page=errorMotinorSettings">Network admin settings</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php else:?>
					<div style="width:79%;" class="postbox-container">
						<div class="meta-box-sortables ui-sortable" style="min-height: 120px;" id="normal-sortables">
							<div class="postbox" id="network_dashboard_right_now">
								<h3 class="hndle"><span>Options</span></h3>
								<div class="inside">
									<ul>
										<li><a href="<?php echo (errorMonitor_DataTools::isNetworkInstall())?network_admin_url():admin_url()?>admin.php?page=errorMotinorSettings">Settings</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>
				<?php include_once(ERROR_REPORT_PLUGIN_DIR.'/includes/postbox.php');?>
			</div>
		</div>
	</div>
	<br class="clear" />
	<h3>Error list</h3>
	<table class="widefat error_monitor-error-list" cellspacing="0">
		<thead>
			<tr>
				<th>URL</th>
				<th>Count</th>
				<th>Referer</th>
				<th>Last Error</th>
				<th>
					Delete 
					<?php if(!$blog_id):?>
						<a id="" class="button-primary error_monitor-delete-all" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php">Delete all</a>
					<?php else:?>
						<span class="error_monitor-delete-all-blog"><a id="<?php echo $blog_id;?>" class="button-primary" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php">Delete all</a></span>
					<?php endif;?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$errorsRowset = $error->getErrorList($blog_id);
			if(sizeof($errorsRowset) == 0){ ?>
				<tr>
					<td colspan="5" style="text-align:center;">No errors (minimum hit count: <?php  echo errorMonitor_DataTools::getPluginOption("min_hit_count",null,true);?>)</td>
				</tr>
			<?php }
			foreach($errorsRowset as $row){
			
				if($previousBlogId != $row->blog_id && is_network_admin()){
					$domain = $error->getDomain($row);
					?>
					<tr id="blog<?php echo $row->blog_id;?>">
						<td colspan="4"><strong><a target="_blank"  href="<?php echo get_admin_url($row->blog_id);?>options-general.php?page=errorMonitor"><?php echo $domain;?></a></strong>   <a target="_blank" href="http://<?php echo $domain;?>">visit</a></td>
						<td class="error_monitor-delete-all-blog" ><a id="<?php echo $row->blog_id;?>" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php">delete entries for this blog</a></td>
					</tr>
					<?php 
				}
				?>
				<tr class="error_monitor-error-row" id="<?php echo $row->blog_id;?>">
					<td><a target="_blank" href="http://<?php echo $domain.$row->url;?>"><?php echo $row->url;?></a></td>
					<td><?php echo $row->count;?></td>
					<td>
					<?php if($row->referer != ""):?>
						<a target="_blank" href="<?php echo $row->referer;?>"><?php echo $row->referer;?></a></td>
					<?php else:?>
						--
					<?php endif;?>
					<td><?php echo mysql2date(get_option('date_format'), $row->last_error);?>,  <?php echo mysql2date(get_option('time_format'), $row->last_error);?></td>
					<td><a class="button-secondary" id="<?php echo $row->id;?>" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php">Delete</a></td>
				</tr>
			<?php 
			 $previousBlogId = $row->blog_id;
			}?>
		</tbody>
	</table>
</div><?php 