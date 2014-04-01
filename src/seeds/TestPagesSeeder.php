<?php namespace Hokeo\Vessel\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestPagesSeeder extends Seeder {

	public function run()
	{
		DB::table('vessel_pages')->delete();
		
		$pages = array(
			// [
			// 	'values' => [
			// 		'edit' => null, // edit # or null
			// 		'slug' => '',
			// 		'title' => '',
			// 		'description' => '',
			// 		'nest_url' => true,
			// 		'visible' => true,
			// 		'in_menu' => true,
			// 		'formatter' => 'Markdown',
			// 		'template' => null,
			// 		'created_at' => Carbon\Carbon::parse('now'),
			// 		'updated_at' => Carbon\Carbon::parse('now'),
			// 	],
			// 	'parent' => null, // slug of parent, or null
			// 	'content' => '',
			// 	'user' => 'andrew', // username
			// ],
			
			[
				'values' => [
					'edit' => null, // edit # or null
					'slug' => 'test-page-one',
					'title' => 'Test Page One',
					'description' => 'This is a test page. Numero uno.',
					'nest_url' => true,
					'visible' => true,
					'in_menu' => true,
					'formatter' => 'Markdown',
					'template' => null,
					'created_at' => Carbon\Carbon::parse('now - 5 hours'),
					'updated_at' => Carbon\Carbon::parse('now'),
				],
				'parent' => null, // slug of parent, or null
				'content' => '',
				'user' => 'andrew', // username
			],

		);

		foreach ($pages as $page)
		{
			$node = \Hokeo\Vessel\Page::create($page['values']);

			if ($page['parent'])
			{
				$parent = \Hokeo\Vessel\Page::where('slug', $page['parent'])->first();
				if ($parent) $node->makeChildOf($parent);
			}
		}
	}

}
