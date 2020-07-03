<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller
{
    public function get($username = NULL)
    {
        // date=week,month,year
        $data = array();
        
        $this->load->library('user/lib_user');
        if (is_null($data['user'] = $this->lib_user->get_by_username($username)))
        {
            show_404();
        }

        $this->load->library('service/lib_post_feed');
        $data['post_list'] = $this->lib_post_feed->search($data['user']['user_id'],
            $this->input->get('q'),
            $this->input->get('paginate_by'),
            $this->input->get('offset'),
            $this->input->get('page'),
            $this->input->get('date'));

        $this->_output_search($data['user'], $data['post_list']);
    }

    private function _output_search($user, $post_list)
    {
        $output = [
            'count'         => $post_list['count'],
            'has_next'      => $post_list['has_next'],
            'next_page'     => $this->input->get('page') + 1,
            'object_list'   => [],
            'current'       => $this->input->get('page'),
            'per_page'      => 12,
            'pages'         => ceil($post_list['count'] / 12),
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
                'title'             => '',
                'summary'           => '',
                'url'               => weblog_url($user, $weblog, $post['slug'].'~'.$post['post_id']),
                'published_at'      => $post['published'].'+00:00',
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
                'image_obj'         => NULL,
            ];

            if (!empty($post['title']))         $object['title'] = $post['title'];
            if (!empty($post['description']))   $object['summary'] = $post['description'];
            
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

    public function type_ahead($username = NULL)
    {
        $data = array();
        
        $this->load->library('user/lib_user');
        if (is_null($data['user'] = $this->lib_user->get_by_username($username)))
        {
            show_404();
        }

        $this->load->library('service/lib_post_feed');
        $data['post_list'] = $this->lib_post_feed->search($data['user']['user_id'],
            $this->input->get('q'), 4);

        $this->_output($data['user'], $data['post_list']);
    }

    private function _output($user, $post_list)
    {
        $output = [
            'has_next'      => FALSE,
            'next_page'     => NULL,
            'object_list'   => [],
            // 'current'       => 0,
            // 'per_page'      => 0,
            // 'pages'         => 0,
        ];

        if ($post_list['has_next'])
        {
            $output['has_next'] = TRUE;
            $output['next_page'] = $this->input->get('page') + 1;
        }

        $this->load->library('user/lib_weblog');
        $weblog = $this->lib_weblog->get_by_id($user['user_id']);

        foreach ($post_list['items'] as $post)
        {
            $title = $post['title'].' '.$post['description'];
            $title = character_limiter($title, 80, '');

            $object = [
                'url' => weblog_url($user, $weblog, $post['slug'].'~'.$post['post_id']),
                'content_type_display_name' => service_name($post['service'], $post['kind']),
                'title' => $title,
            ];

            $output['object_list'][] = $object;
        }

        header('Content-Type: application/json');
        print_r( json_encode($output) ); 
        die();
    }
}

// // 20161217142401
// // https://blog.google/api/v1/type-ahead/?q=goo

// {
//   "count": 440,
//   "has_next": true,
//   "next_page": 2,
//   "object_list": [
//     {
//       "url": "/topics/google-asia/new-home-google-singapore/",
//       "content_type_display_name": "Article",
//       "title": "A new home for Google in Singapore"
//     },
//     {
//       "url": "/topics/google-asia/virtually-explore-indonesias-wonders-google-arts-culture/",
//       "content_type_display_name": "Article",
//       "title": "Virtually explore Indonesia's wonders with Google Arts & Culture"
//     },
//     {
//       "url": "/topics/google-asia/google-india-making-our-products-work-better-everyone/",
//       "content_type_display_name": "Article",
//       "title": "Google for India: Making our products work better for everyone"
//     },
//     {
//       "url": "/topics/google-asia/a-pitch-perfect-end-to-googles-first/",
//       "content_type_display_name": "Article",
//       "title": "A pitch-perfect end to Google’s first Launchpad Week in Jakarta"
//     }
//   ],
//   "current": 1,
//   "per_page": 4,
//   "pages": 110
// }