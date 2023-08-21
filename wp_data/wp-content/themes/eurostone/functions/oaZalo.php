<?php
  add_action('wpcf7_mail_sent', 'call_api_on_cf7_submit');

  function call_api_on_cf7_submit($contact_form) {
      // Replace these values with your actual API credentials
      $apiUsername = "api@Dentmark.vn";
      $apiPassword = "9a9sd98e7mm435d6drgg45672a1fae46789";
      $url = "https://api-sms.mitek.vn/v1/zalo/send";

      // Get the form data from Contact Form 7
      $submission = WPCF7_Submission::get_instance();
      if (!$submission) {
          return; // Exit if the form submission is not available
      }

      // Customize the template data here using the form data
      $formData = $submission->get_posted_data();
      $formId = isset($formData['form_id']) ? sanitize_text_field($formData['form_id']) : 'N/A'; // Replace this with the actual form ID
      $tel = isset($formData['tel']) ? sanitize_text_field($formData['tel']) : '';
      $user = isset($formData['user']) ? sanitize_text_field($formData['user']) : '';
      $templateData = array(
          "customer_name" => $user,
          "customer_id" => $formId,
          "order_code" => $formId,
          "phone" => $tel,
          "date" => date("Y-m-d")
      );

      // Prepare the API request data
      $data = array(
          "oa_id" => "4299371072036201150",
          "phone" => $tel,
          "template_id" => 255398,
          "template_data" => $templateData,
          "tracking_id" => $formId
      );

      // Set up the API request options
      $headers = array(
          "Content-Type: application/json",
          "Authorization: Basic " . base64_encode("$apiUsername:$apiPassword")
      );

      // Send the API request using cURL
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);

      // Check for cURL errors
      if (curl_errno($ch)) {
          error_log("cURL Error: " . curl_error($ch));
      }

      curl_close($ch);

      // Log the API response (optional)
      if ($response) {
          error_log($response);
      } else {
          error_log("API request failed.");
      }
  }
