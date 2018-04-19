<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Coffee Theme
*
* PHP version >= 5.4
*
* @category  PHP
* @package   VideoTube - PHP Script
* @author    Nicolas Grimonpont <support@coffeetheme.com>
* @copyright 2010-2017 Nicolas Grimonpont
* @license   Standard License
* @link      http://coffeetheme.com/
*/

class Videos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!isset($this->session->admin)) {
            redirect('/login/');
        }
        $this->lang->load('front', $this->session->site_lang);
        $data = $this->autoloadModel->getNotifications();
        $content = $this->load->view('dashboard/template', $data, true);
        $this->load->model(array('videosModel'));
    }

    public function index()
    {
        $data['title'] = $this->lang->line('All Videos');
        // Deleting a video
        $idVideo = $this->input->get('del', true);
        if(isset($idVideo) && !$this->config->item('demo')) {
            $this->videosModel->delVideo($idVideo);
        }
        // Viewing videos
        $data['getVideos'] = $this->videosModel->getVideos();
        $content = $this->load->view('dashboard/videos', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function episodes()
    {
        $data['title'] = $this->lang->line('All episodes');
        // Deleting an episode
        $idEpisode = $this->input->get('del', true);
        if(isset($idEpisode) && !$this->config->item('demo')) {
            $this->videosModel->delEpisode($idEpisode);
        }
        // Viewing episodes
        $data['getVideos'] = $this->videosModel->getEpisodes();
        $content = $this->load->view('dashboard/episodes', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function add()
    {
        $data['title'] = $this->lang->line('New Video');
        // Processing the Add Form
        $postTitle = $this->input->post('title', true);
        $postURL = $this->input->post('url', true);
        $postDescription = $this->input->post('description', true);
        $postIdCategory = $this->input->post('category', true);
        $postKeywords = $this->input->post('keywords', true);
        $postType = $this->input->post('type', true);
        $postEmbed = $this->input->post('embed', true);
        $postSubscription = $this->input->post('subscription', true);
        $postStatus = $this->input->post('status', true);
        if(($postTitle) != '' && !$this->config->item('demo')) {
            if($postURL == '') {
                $postURL = url_title(convert_accented_characters($postTitle), $separator = '-', $lowercase = true);
            } else {
                $postURL = url_title(convert_accented_characters($postURL), $separator = '-', $lowercase = true);
            }
            if(!empty($postKeywords)) {
                $postKeywords = array_map("addQuote", $postKeywords);
                $postKeywords = implode(",", $postKeywords);
            }
            $data['msg'] = $this->videosModel->addVideo($postTitle, $postURL, $postDescription, $postIdCategory, $postKeywords, $postType, $postEmbed, $postSubscription, $postStatus);
        }
        // Get categories
        $data['getCategories'] = $this->videosModel->getCategories();
        // Get keywords
        $data['getKeywords'] = $this->videosModel->getKeywords();
        $content = $this->load->view('dashboard/video_edit', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function edit($idVideo = '')
    {
        $data['title'] = $this->lang->line('Videos');
        // Processing the Change Form
        $postTitle = $this->input->post('title', true);
        $postURL = $this->input->post('url', true);
        $postDescription = $this->input->post('description', true);
        $postIdCategory = $this->input->post('category', true);
        $postKeywords = $this->input->post('keywords', true);
        $postType = $this->input->post('type', true);
        $postEmbed = $this->input->post('embed', true);
        $postSubscription = $this->input->post('subscription', true);
        $postStatus = $this->input->post('status', true);
        if(isset($postTitle) && ($postTitle) != '' && !$this->config->item('demo')) {
            if($postURL == '') {
                $postURL = url_title(convert_accented_characters($postTitle), $separator = '-', $lowercase = true);
            } else {
                $postURL = url_title(convert_accented_characters($postURL), $separator = '-', $lowercase = true);
            }
            if(!empty($postKeywords)) {
                $postKeywords = array_map("addQuote", $postKeywords);
                $postKeywords = implode(",", $postKeywords);
            }
            $data['msg'] = $this->videosModel->editVideo($idVideo, $postTitle, $postURL, $postDescription, $postIdCategory, $postKeywords, $postType, $postEmbed, $postSubscription, $postStatus);
        }
        // Processing the form for sending the image
        if(null !== $this->input->post('hiddenImage') && !$this->config->item('demo')) {
            $data = array_merge($data, $this->uploadImage($idVideo, 0));
        }
        // Processing the form for sending the video
        if(null !== $this->input->post('hiddenFile') && !$this->config->item('demo')) {
            $data = array_merge($data, $this->uploadFile($idVideo, 0));
        }
        // Processing the form for sending the video from input
        if($this->input->post('userInput') && !$this->config->item('demo')) {
            $updateFile = $this->videosModel->updateFile($idVideo, $this->input->post('userInput'));
            if($updateFile) {
                $data['msg'] = alert($this->lang->line('The file was successfully update'));
            }
        }
        // Get video data
        $data = array_merge($data, $this->videosModel->getVideo($idVideo));
        // Get categories
        $data['getCategories'] = $this->videosModel->getCategories($data['id_category']);
        // Get keywords
        $data['getKeywords'] = $this->videosModel->getKeywords($idVideo);
        $content = $this->load->view('dashboard/video_edit', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function addepisode()
    {
        $data['title'] = $this->lang->line('New episode');
        $post['video'] = $this->input->post('video', true);
        $post['title'] = $this->input->post('title', true);
        $post['description'] = $this->input->post('description', true);
        $post['episode'] = $this->input->post('episode', true);
        $post['season'] = $this->input->post('season', true);
        $post['image'] = $this->input->post('image', true);
        $post['type'] = $this->input->post('type', true);
        $post['embed'] = $this->input->post('embed', true);
        $post['status'] = $this->input->post('status', true);
        if(!empty($post['title']) && !$this->config->item('demo')) {
            $data['msg'] = $this->videosModel->addEpisode($post);
        }
        $data['getVideosList'] = $this->videosModel->getVideosList();
        $content = $this->load->view('dashboard/episode_edit', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function editepisode($idVideo = '')
    {
        $data['title'] = $this->lang->line('Videos');
        $post['video'] = $this->input->post('video', true);
        $post['title'] = $this->input->post('title', true);
        $post['description'] = $this->input->post('description', true);
        $post['episode'] = $this->input->post('episode', true);
        $post['season'] = $this->input->post('season', true);
        $post['image'] = $this->input->post('image', true);
        $post['type'] = $this->input->post('type', true);
        $post['embed'] = $this->input->post('embed', true);
        $post['status'] = $this->input->post('status', true);
        if ($post['title'] != '' && !$this->config->item('demo')) {
            $data['msg'] = $this->videosModel->editEpisode($post, $idVideo);
        }
        // Processing the form for sending the image
        if(null !== $this->input->post('hiddenImage') && !$this->config->item('demo')) {
            $data = array_merge($data, $this->uploadImage($idVideo, 1));
        }
        // Processing the form for sending the video
        if(null !== $this->input->post('hiddenFile') && !$this->config->item('demo')) {
            $data = array_merge($data, $this->uploadFile($idVideo, 1));
        }
        // Processing the form for sending the video from input
        if($this->input->post('userInput') && !$this->config->item('demo')) {
            $updateFile = $this->videosModel->updateFile($idVideo, $this->input->post('userInput'), 1);
            if($updateFile) {
                $data['msg'] = alert($this->lang->line('The file was successfully update'));
            }
        }
        $data = array_merge($data, $this->videosModel->getEpisode($idVideo));
        // Update parent video type
        $this->videosModel->updateVideoType($data['id_relation']);
        $data['getVideosList'] = $this->videosModel->getVideosList($data['id_relation']);
        $content = $this->load->view('dashboard/episode_edit', $data, true);
        $this->load->view('dashboard/template', array('content' => $content));
    }

    public function uploadImage($id, $type) {
        $config['upload_path']   = './uploads/images/videos/';
        $config['allowed_types'] = 'jpg|jpeg|gif|png';
        $config['max_size']      = 5000;
        $config['max_width']     = 5048;
        $config['max_height']    = 5536;
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('userImage')) {
            $data['error'] = $this->upload->display_errors();
        } else {
            $data['msg'] = alert($this->lang->line('The file was successfully sent'));
            $this->videosModel->updateImage($id, site_url('uploads/images/videos/'.$this->upload->data('file_name')), $type);
        }
        return $data;
    }

    public function uploadFile($id, $type) {
        $config['upload_path']   = './uploads/files/videos/';
        $config['allowed_types'] = 'mp4|mpeg|mov|ogg|webm|flv';
        $config['max_size']      = 500000;
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('userFile')) {
            $data['error'] = $this->upload->display_errors();
        } else {
            $data['msg'] = alert($this->lang->line('The file was successfully sent'));
            $this->videosModel->updateFile($id, site_url('uploads/files/videos/'.$this->upload->data('file_name')), $type);
        }
        return $data;
    }
}
