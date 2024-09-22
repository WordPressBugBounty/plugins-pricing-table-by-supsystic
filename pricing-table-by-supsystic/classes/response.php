<?php
#[\AllowDynamicProperties]
class responsePts {
   public $code = 0;
   public $error = false;
   public $errors = array();
   public $messages = array();
   public $html = '';
   public $data = array();
   private $_ignoreShellData = false;
   public function getReqType() {
      return reqPts::getVar('reqType');
   }
   public function isAjax() {
      return $this->getReqType() == 'ajax';
   }
   public function ajaxExec($forceAjax = false) {
      $isAjax = $this->isAjax();
      $redirect = reqPts::getVar('redirect');
      if (count($this->errors) > 0) $this->error = true;
      if ($isAjax || $forceAjax) exit(json_encode($this));
      return $this;
   }
   public function mainRedirect($redirectUrl = '') {
      $redirectUrl = empty($redirectUrl) ? PTS_SITE_URL : $redirectUrl;
      $redirectData = array();
      if (!empty($this->errors)) {
         $redirectData['ptsErrors'] = $this->errors;
      }
      if (!empty($this->messages)) {
         $redirectData['ptsMsgs'] = $this->messages;
      }
      return redirectPts($redirectUrl . (strpos($redirectUrl, '?') ? '&' : '?') . http_build_query($redirectData));
   }
   public function error() {
      return $this->error;
   }
   public function addError($error, $key = '') {
      if (empty($error)) return;
      $this->error = true;
      if (is_array($error)) $this->errors = array_merge($this->errors, $error);
      else {
         if (empty($key)) $this->errors[] = $error;
         else $this->errors[$key] = $error;
      }
   }
   public function pushError($error, $key = '') {
      return $this->addError($error, $key);
   }
   public function addMessage($msg) {
      if (empty($msg)) return;
      if (is_array($msg)) $this->messages = array_merge($this->messages, $msg);
      else $this->messages[] = $msg;
   }
   public function getMessages() {
      return $this->messages;
   }
   public function setHtml($html) {
      $this->html = $html;
   }
   public function addData($data, $value = NULL) {
      if (empty($data)) return;
      if ($this->_ignoreShellData) {
         if (!is_array($data)) $data = array(
            $data => $value
         );
         foreach ($data as $key => $val) {
            $this->{$key} = $val;
         }
      }
      else {
         if (is_array($data)) $this->data = array_merge($this->data, $data);
         else $this->data[$data] = $value;
      }
   }
   public function getErrors() {
      return $this->errors;
   }
   public function ignoreShellData() {
      $this->_ignoreShellData = true;
   }
}
