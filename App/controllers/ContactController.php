<?php // /app/controllers/ContactController.php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

class ContactController extends BaseController {
    /**
     * Display the contact page
     * 
     * @return string Rendered HTML
     */
    public function index() {
        // Data to pass to the template
        $data = [
            'page_title' => 'Contact - Blade',
            'contact_email' => 'contact@example.com',
            'contact_phone' => '+33 1 23 45 67 89'
        ];

        // Render the template
        return $this->render('pages.activite', $data);
    }
    
    /**
     * Process the contact form submission
     * 
     * @return string Rendered HTML
     */
    public function submitForm() {
        // Simulate form processing
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';
        
        // Validate form data (simplified)
        $errors = [];
        if (empty($name)) $errors['name'] = 'Le nom est requis';
        if (empty($email)) $errors['email'] = 'L\'email est requis';
        if (empty($message)) $errors['message'] = 'Le message est requis';
        
        // If there are errors, return to the form with errors
        if (!empty($errors)) {
            return $this->render('pages.contact', [
                'page_title' => 'Contact - Blade',
                'contact_email' => 'contact@example.com',
                'contact_phone' => '+33 1 23 45 67 89',
                'errors' => $errors,
                'old' => $_POST
            ]);
        }
        
        // Process the form (e.g., send email)
        // ...
        
        // Return success message
        return $this->render('pages.contact_success', [
            'page_title' => 'Message Envoyé - Blade',
            'name' => $name
        ]);
    }
}
