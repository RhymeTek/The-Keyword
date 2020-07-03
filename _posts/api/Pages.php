<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
    // paginate_by=10 offset=5 page=1
    public function all($username = NULL)
    {
        $data = array();
        
        $this->load->library('user/lib_user');
        if (is_null($data['user'] = $this->lib_user->get_by_username($username)))
        {
            show_404();
        }

        $this->load->library('service/lib_post_feed');
        $data['post_list'] = $this->lib_post_feed->get_page_latest($data['user']['user_id'],
            $this->input->get('paginate_by'),
            $this->input->get('offset'),
            $this->input->get('page'));

        $this->_output($data['user'], $data['post_list']);
    }

    public function collection($username = NULL, $type = NULL)
    {
        $data = array();
        
        $this->load->library('user/lib_user');
        if (is_null($data['user'] = $this->lib_user->get_by_username($username)))
        {
            show_404();
        }

        $this->load->library('service/lib_post_feed');
        $data['post_list'] = $this->lib_post_feed->get_page_latest_by_type($data['user']['user_id'],
            $type,
            $this->input->get('paginate_by'),
            $this->input->get('offset'),
            $this->input->get('page'));

        $this->_output($data['user'], $data['post_list']);
    }

    public function topic($username = NULL, $word = NULL)
    {
        $data = array();
        
        $this->load->library('user/lib_user');
        if (is_null($data['user'] = $this->lib_user->get_by_username($username)))
        {
            show_404();
        }

        $this->load->library('service/lib_post_feed');
        $data['post_list'] = $this->lib_post_feed->get_page_latest_by_word($data['user']['user_id'],
            $word,
            $this->input->get('paginate_by'),
            $this->input->get('offset'),
            $this->input->get('page'));

        $this->_output($data['user'], $data['post_list']);
    }

    private function _output($user, $post_list)
    {
        $output = [
            'has_next' => FALSE,
            'next_page' => NULL,
            'object_list' => [],
            // 'current'       => 0,
            // 'per_page'      => 0,
            // 'pages'         => 0,
        ];

        if (!empty($post_list['next_id']))
        {
            $output['has_next'] = TRUE;
            $output['next_page'] = $this->input->get('page') + 1;
        }

        $user_full_name = $user['full_name'];
        $user_full_name = explode(' ', $user_full_name);

        $user_first_name = current($user_full_name);
        $user_last_name = end($user_full_name);

        if ($user_first_name == $user_last_name) $user_last_name = NULL;

        $this->load->library('user/lib_weblog');
        $weblog = $this->lib_weblog->get_by_id($user['user_id']);

        foreach ($post_list['items'] as $post)
        {
            $object = [
                'id'                => $post['post_id'],
                'title'             => $post['title'],
                'summary'           => $post['description'],
                'url'               => weblog_url($user, $weblog, $post['slug'].'~'.$post['post_id']),
                'published_at'      => $post['published'].'+00:00',
                'accent_color'      => 'uni-theme-'.get_theme_color($post['service'] . $post['kind']),
                'primary_tag_obj'   => [
                    'active'            => true,
                    'analytics_type'    => 'product',
                    'landing_page_url'  => '/products/search/',
                    'display_name'      => service_name($post['service'], $post['kind'])
                ],
                'authors_obj'       => [
                    'first_name'        => $user_first_name,
                    'last_name'         => $user_last_name,
                    'middle_name'       => '',
                    'company'           => '',
                    'department'        => '',
                    'display_name'      => $user['full_name'],
                    'job_title'         => $user['bio']
                ],
            ];
            
            if (!empty($post['thumbnail_url']))
            {
                $object['image_obj'] = [
                    'sources' => [
                        [
                            'url'   => $post['thumbnail_url'],
                            'media' => '(max-resolution: 1.5dppx)',
                            'srcset'=> $post['thumbnail_url'].' 120w',
                            'width' => 120,
                            'sizes' => '120px',
                        ],[
                            'url'   => $post['thumbnail_url'],
                            'media' => '(max-resolution: 1.5dppx)',
                            'srcset'=> $post['thumbnail_url'].' 240w',
                            'width' => 240,
                            'sizes' => '240px',
                        ],
                    ],
                    'alt'   => $post['post_id'],
                    'sizes' => '120px, 240px',
                    'srcset'=> $post['thumbnail_url'].' 120w, '.$post['thumbnail_url'].' 240w',
                    'src'   => $post['thumbnail_url'],
                ];
            }

            $output['object_list'][] = $object;
        }

        header('Content-Type: application/json');
        print_r( json_encode($output) ); 
        die();
    }
}

// // 20161217143957
// // https://blog.google/api/v1/pages/2/latest-articles/?paginate_by=10&offset=5&page=1

// {
//   "count": 1339,
//   "has_next": true,
//   "next_page": 2,
//   "object_list": [
//     {
//       "title": "Campus Exchange in Korea inspires start-ups’ minds, hearts and “Seoul”",
//       "url": "/topics/google-asia/campus-exchange-korea-inspires-start-ups-minds-hearts-and-seoul/",
//       "published_at": "2016-12-15 08:00:00+00:00",
//       "summary": "Read about some of the things CoolJam from Korea and VRMonkey from Brazil learned at the latest Campus Exchange for start-ups held at Campus Seoul. ",
//       "primary_tag_obj": {
//         "active": true,
//         "analytics_type": "topic",
//         "landing_page_url": "/topics/google-asia/",
//         "display_name": "Google in Asia"
//       },
//       "authors_obj": [
//         {
//           "first_name": "Nabil ",
//           "last_name": "Naghdy",
//           "middle_name": "",
//           "company": "",
//           "department": "Google Flights",
//           "display_name": "Nabil  Naghdy",
//           "job_title": "Product Manager"
//         }
//       ],
//       "image_obj": {
//         "sources": [
//           {
//             "url": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-120x120.jpg",
//             "media": "(max-resolution: 1.5dppx)",
//             "srcset": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-120x120.jpg 120w",
//             "width": 120,
//             "sizes": " 120px"
//           },
//           {
//             "url": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-240x240.jpg",
//             "media": "(min-resolution: 1.5dppx)",
//             "srcset": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-240x240.jpg 240w",
//             "width": 240,
//             "sizes": " 240px"
//           }
//         ],
//         "alt": "Campus Exchange in Seoul",
//         "sizes": " 120px,  240px",
//         "srcset": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-120x120.jpg 120w, https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-240x240.jpg 240w",
//         "src": "https://storage.googleapis.com/gweb-uniblog-publish-prod/images/IMG_4322-2.2e16d0ba.fill-240x240.jpg"
//       },
//       "id": 23329
//     },
//     
//     ...
//     
//   ],
//   "current": 1,
//   "per_page": 10,
//   "pages": 134
// }