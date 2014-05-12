<?php 

class MobileController extends \BaseController {

	/**
	 * Mendapatkan login credentials
	 * @url <domain>/mobile/login/{id}/{pass}
	 */
	public function getLogin($id, $pass, $grup)
	{
		$user = User::findByIdPass($id, $pass);
		$user = $user->first();

		if (!$user)
		{
			$user = new StdClass;
			$user->login = 'false';
			$user->no_id = '';
			$user->nama = '';
			$user->grup = '';
			$user->tmp_lhr = '';
			$user->tgl_lhr = '';
			$user->bln_lhr = '';
			$user->thn_lhr = '';
			$user->gender = '';
			$user->alamat = '';
			$user->pekerjaan = '';
			$user->no_hp = '';
			$user->email = '';
		}
		else
		{
			$dates = explode('-', $user->tgl_lhr);
			
			$user->type = $user->grup;
			$user->tgl_lhr = $dates['2'];
			$user->bln_lhr = $dates['1'];
			$user->thn_lhr = $dates['0'];
			if(($grup == 0 && $user->type == 4) || ($grup == 1 && $user->type != 4)) 
				$user->login = 'true';
			else
				$user->login = 'false';
		}

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<login>'.$user->login.'</login>' .
				'<no_id>'.$user->no_id.'</no_id>' .
				'<nama>'.$user->nama.'</nama>' .
				'<grup>'.$user->grup.'</grup>' .
				'<tmp_lhr>'.$user->tmp_lhr.'</tmp_lhr>' .
				'<tgl_lhr>'.$user->tgl_lhr.'</tgl_lhr>' .
				'<bln_lhr>'.$user->bln_lhr.'</bln_lhr>' .
				'<thn_lhr>'.$user->thn_lhr.'</thn_lhr>' .
				'<gender>'.$user->gender.'</gender>' .
				'<alamat>'.$user->alamat.'</alamat>' .
				'<pekerjaan>'.$user->pekerjaan.'</pekerjaan>' .
				'<no_hp>'.$user->no_hp.'</no_hp>' .
				'<email>'.$user->email.'</email>' .
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Register User
	 * @url <domain>/mobile/user/register/{id}/{nama}/{tmp_lahir}/{tgl}/{bln}/{thn}/{gender}/{alamat}/{kerja}/{telp}/{email}
	 */
	public function postUserRegister($id, $pass, $nama, $tmp_lhr, $tgl, $bln, $thn, $gender, $alamat, $kerja, $telp, $email)
	{
		if (User::find($id)) 
		{
			$status = 'ID sudah terdaftar';
		}
		else
		{
			$user = new User;

			$user->no_id = $id;
			$user->pass = $pass;
			$user->nama = $nama;
			$user->tmp_lhr = $tmp_lhr;
			$user->tgl_lhr = $thn . '-' . $bln . '-' . $tgl;
			$user->gender = $gender;
			$user->alamat = $alamat;
			$user->pekerjaan = $kerja;
			$user->no_hp = $telp;
			$user->email = $email;
			$user->grup = 4; // sipil

			$user->save();

			$status = 'Sukses';
		}

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'. $status .'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Update User
	 * @url <domain>/mobile/user/register/{id}/{nama}/{tmp_lahir}/{tgl}/{bln}/{thn}/{gender}/{alamat}/{kerja}/{telp}/{email}
	 */
	public function putUserUpdate($id, $nama, $tmp_lhr, $tgl, $bln, $thn, $gender, $alamat, $kerja, $telp, $email)
	{
		$user = User::find($id);

		if ($user)
		{
			$user->nama = $nama;
			$user->tmp_lhr = $tmp_lhr;
			$user->tgl_lhr = $thn . '-' . $bln . '-' . $tgl;
			$user->gender = $gender;
			$user->alamat = $alamat;
			$user->pekerjaan = $kerja;
			$user->no_hp = $telp;
			$user->email = $email;

			$user->save();

			$status = 'Sukses';
		}
		else
		{
			$status = 'ID tidak ditemukan';
		}

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	* Change User Password
	* @url <domain>/mobile/{id}/{old_pass}/{new_pass}
	*/
	public function getChangePassword($id, $old_pass, $new_pass)
	{
		$user = User::where('no_id',$id)->where('pass',$old_pass)->first();

		if($user)
		{
			$user->pass = $new_pass;
			$user->save();
			$status = "Password berhasil diupdate";
		}
		else
			$status = "Password salah";

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Verify User
	 * @url <domain>/mobile/user/verify/{id}
	 */
	public function getVerifyUser($id)
	{
		if (User::find($id)) 
			$status = 'valid';
		else
			$status = 'invalid';

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'.
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	public function getEM()
	{
		$emergency = EmergencyCase::GetActive();
		$items = '<em_case>';

		foreach ($emergency as $key => $em) 
		{
			if($em->validator)
				$validator = User::GetName($em->validator)->first()->nama;
			else
				$validator = "";
			$items .= '<item>' . 
				'<em_id>'.$em->case_id.'</em_id>'.
				'<type>'.EmergencyType::find($em->type)->type_name.'</type>' .
				'<desc>'.$em->desc.'</desc>'.
				'<lon>'.$em->lon.'</lon>' .
				'<lat>'.$em->lat.'</lat>' .
				'<near>'.RoadSmg::FindNearestRoad($em->lat, $em->lon)->street_name.'</near>' .
				'<time>'.$em->time.'</time>' .
				'<reporter>'.User::GetName($em->reporter)->first()->nama.'</reporter>' .
				'<validator>'.$validator.'</validator>'.
				'<status>'.$em->status.'</status>'.
				'</item>';
		}
		$items .= '</em_case>';
		
		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				$items.
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Send Emergency
	 * @url <domain>/mobile/emergency/send/{id}/{type}/{lng}/{lat}/{desc}
	 */
	public function postEmergencySend($id, $type, $lng, $lat, $desc, $user_type)
	{
		$user = User::find($id);
		$em_type = EmergencyType::find($type);

		if ($user && $em_type)
		{
			$em_case = new EmergencyCase;

			$em_case->type = $type;
			$em_case->lon = $lng;
			$em_case->lat = $lat;
			$em_case->desc = $desc;
			$em_case->reporter = $id;
			$em_case->time = DB::raw('NOW()');
			
			if($user_type == 1)
			{
				$em_case->validator = $id; // admin
				$em_case->status = 1; // valid
			}

			$em_case->save();

			$status = 'Sukses';
		}
		else
		{
			$status = 'No ID / Jenis emergency tidak ditemukan';
		}

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Validate Emergency
	 * @url <domain>/mobile/emergency/validate/{user_id}/{em_id}
	 */
	public function setEmergencyValid($user_id, $em_id)
	{
		$user = User::find($user_id);
		$em_case = EmergencyCase::find($em_id);

		if ($user && $em_case)
		{
			if($em_case->validator)
			{
				$status = 'Sudah divalidasi oleh petugas lain';
			}
			else
			{
				$em_case->validator = $user_id;
				$em_case->status = 1;
				$em_case->save();	
				$status = 'Kasus berhasil dikonfirmasi';
			}
		}
		else
		{
			$status = 'ID tidak ditemukan';
		}
		
		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	public function setEmergencyFake($user_id, $em_id)
	{
		$user = User::find($user_id);
		$em_case = EmergencyCase::find($em_id);

		if ($user && $em_case)
		{
			if($em_case->validator)
			{
				$status = 'Sudah divalidasi oleh petugas lain';
			}
			else
			{
				$em_case->validator = $user_id;
				$em_case->status = 0;
				$em_case->save();	
				$status = 'Kasus berhasil dikonfirmasi';
			}
		}
		else
		{
			$status = 'ID tidak ditemukan';
		}
		
		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Resolve Emergency
	 * @url <domain>/mobile/emergency/resolve/{user_id}/{em_id}
	 */
	public function setEmergencyResolved($user_id, $em_id)
	{
		$user = User::find($user_id);
		$em_case = EmergencyCase::find($em_id);

		if ($user && $em_case)
		{
			if($em_case->resolver)
			{
				$status = 'Kasus telah ditutup sebelumnya.';
			}
			else
			{
				$em_case->resolver = $user_id;
				$em_case->status = 2;
				$em_case->save();	
				$status = 'Kasus berhasil ditutup.';
			}
		}
		else
		{
			$status = 'ID tidak ditemukan';
		}
		
		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				'<status>'.$status.'</status>'. 
			'</response>';

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Get All Roads
	 * @url <domain>/mobile/roads
	 */
	public function getRoads()
	{
		$streets = RoadSmg::withGeoJson();

		$items = '<roads>';
		foreach ($streets as $key => $street) 
		{
			$items .= '<item>' . 
				'<gid>'.$street->gid.'</gid>' .
				'<dir>'.$street->dir.'</dir>' .
				'<street_name>'.$street->street_name.'</street_name>' .
				'<geo_json>'.$street->geo_json.'</geo_json>' .
				'<to_cost>'.$street->to_cost.'</to_cost>' .
				'<r_cost>'.$street->r_cost.'</r_cost>' .
				'</item>';
		}
		$items .= '</roads>';

		$markers = Facility::allWithGeomToLatLng();

		$items .= '<em_facilities>';
		foreach ($markers as $key => $marker) 
		{
			$items .= '<item>' . 
				'<gid>'.$marker->gid.'</gid>' .
				'<nama>'.$marker->nama.'</nama>' .
				'<type>'.$marker->type.'</type>' .
				'<alamat>'.$marker->alamat.'</alamat>' .
				'<telp>'.$marker->telp.'</telp>' .
				'<lng>'.$marker->lng.'</lng>' . 
				'<lat>'.$marker->lat.'</lat>' . 
				'</item>';
		}
		$items .= '</em_facilities>';

		$data = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				$items .
			'</response>';		

		return Response::make($data, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

	/**
	 * Get Routes
	 * @url <domain>/mobile/routes/{src_lng}/{src_lat}/{dest_lng}/{dest_lat}
	 */
	public function getRoutes($src_lng, $src_lat, $dest_lng, $dest_lat)
	{
		try {
			set_time_limit(0);
		} catch(\Exception $e) {}
		

		$road = new Genetic(
			$src_lat,
			$src_lng,
			$dest_lat,
			$dest_lng
		);
		$data = $road->findBestPath();		
		
		$items = '<betspath>';
		$items .= '<path>' . implode(', ', $data['bestpath']['path']) . '</path>'.
			'<cost>'. $data['bestpath']['cost'] .'</cost>'.
			'<src_part>'. $data['bestpath']['src_part'] .'</src_part>'.
			'<dest_part>'. $data['bestpath']['dest_part'] .'</dest_part>'.
			'<src_point>'. 
			'<lng>' . $data['bestpath']['src_point']['lng'] . '</lng>'.
			'<lat>' . $data['bestpath']['src_point']['lat'] . '</lat>'.
			'</src_point>'.
			'<dest_point>'. 
			'<lng>' . $data['bestpath']['dest_point']['lng'] . '</lng>'.
			'<lat>' . $data['bestpath']['dest_point']['lat'] . '</lat>'.
			'</dest_point>'.
			'<src_dir>'. $data['bestpath']['src_dir'] .'</src_dir>'.
			'<dest_dir>'. $data['bestpath']['dest_dir'] .'</dest_dir>';
		$items .= '</betspath>';

		$firstPath = array_shift($data['bestpath']['path']);
		$lastPath = array_pop($data['bestpath']['path']);

		$result = array();

		// starting point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-1';
		$obj->dir = 'FT';
		$obj->street_name = 'Start';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['src_lng'], $data['src_lat']),
				array($data['bestpath']['src_point']['lng'], $data['bestpath']['src_point']['lat'])
			)
		));
		$result[] = $obj;

		// intersection between road from starting point
		$src_part = $data['bestpath']['src_part'];
		$result[] = ($data['bestpath']['src_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($firstPath, 0, $data['bestpath']['src_part']) :
			RoadSmg::GeoJsonNearestPoint($firstPath, $src_part, 1);
		
		// routing roads
		foreach ($data['bestpath']['path'] as $value) 
		{
			$result[] = RoadSmg::findWithGeoJson($value);
		}

		// intersection between road from end point
		// $dest_part = 1 - $data['bestpath']['dest_part'];
		$result[] = ($data['bestpath']['dest_dir'] === 0) ?
			RoadSmg::GeoJsonNearestPoint($lastPath, 0, $data['bestpath']['dest_part']) :
			RoadSmg::GeoJsonNearestPoint($lastPath, $data['bestpath']['dest_part'], 1);

		// end point distance to roads
		$obj = new \StdClass;
		$obj->gid = '-2';
		$obj->dir = 'FT';
		$obj->street_name = 'Finish';
		$obj->geo_json = json_encode(array(
			'type' => 'LineString',
			'coordinates' => array(
				array($data['dest_lng'], $data['dest_lat']),
				array($data['bestpath']['dest_point']['lng'], $data['bestpath']['dest_point']['lat'])
			)
		));
		$result[] = $obj;

		$jalan = array();
		$items .= '<routes>';
		foreach ($result as $key => $rs) 
		{
			$items .= '<item>'.
				'<gid>'. $rs->gid .'</gid>'.
				'<dir>'. $rs->dir .'</dir>'.
				'<street_name>'. $rs->street_name .'</street_name>'.
				'<geo_json>'. $rs->geo_json .'</geo_json>'.
			'</item>';

			$jalan[] = $rs->street_name;
			
		}
		$items2 = '<path_jln>'. implode(', ', $jalan) .'</path_jln>';
		$items .= '</routes>';

		$response = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<response>' .
				$items .
				$items2 .
			'</response>';

		return Response::make($response, 200, array(
			'Content-type' => 'text/xml',
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Expose-Headers' => 'Access-Control-Allow-Origin'
		));
	}

}
