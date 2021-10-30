<?php
class Sitemap_Action extends Typecho_Widget implements Widget_Interface_Do
{
	
	
	public function action()
	{
		$db = Typecho_Db::get();
		$options = Typecho_Widget::widget('Widget_Options');

		$pages = $db->fetchAll($db->select()->from('table.contents')
		->where('table.contents.status = ?', 'publish')
		->where('table.contents.created < ?', $options->gmtTime)
		->where('table.contents.type = ?', 'page')
		->order('table.contents.created', Typecho_Db::SORT_DESC));

		$articles = $db->fetchAll($db->select()->from('table.contents')
		->where('table.contents.status = ?', 'publish')
		->where('table.contents.created < ?', $options->gmtTime)
		->where('table.contents.type = ?', 'post')
		->order('table.contents.created', Typecho_Db::SORT_DESC));
		
		

		header("Content-Type: application/xml");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<?xml-stylesheet type='text/xsl' href='" . $options->pluginUrl . "/Sitemap/sitemap.xsl'?>\n";
		echo "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\nxmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
		foreach($pages AS $page) {
			$type = $page['type'];
			$routeExists = (NULL != Typecho_Router::get($type));
			$pathinfo = $routeExists ? Typecho_Router::url($type, $page) : '#';
			$permalink = Typecho_Common::url($pathinfo, $options->index);
			$modified = date('Y-m-d', $page['modified']); 

			echo "\t<url>\n";
			echo "\t\t<loc>".$permalink."</loc>\n";
			echo "\t\t<lastmod>".$modified."</lastmod>\n";
			echo "\t\t<changefreq>always</changefreq>\n";
			echo "\t\t<priority>0.8</priority>\n";
			echo "\t</url>\n";
		}
		foreach($articles AS $article) {
			$data = Typecho_Widget::widget('Widget_Abstract_Contents')->push($article);
            $created = date('Y-m-d', $data['created']);  
            $link = $data['permalink']; 
			echo "\t<url>\n";
			echo "\t\t<loc>".$link."</loc>\n";
			echo "\t\t<lastmod>".$created."</lastmod>\n";
			echo "\t\t<changefreq>always</changefreq>\n";
			echo "\t\t<priority>0.5</priority>\n";
			echo "\t</url>\n";
		}
		echo "</urlset>";
	}
	
}
