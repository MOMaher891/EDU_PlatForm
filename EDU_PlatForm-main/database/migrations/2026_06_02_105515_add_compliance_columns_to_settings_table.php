<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('terms_and_conditions')->nullable();
            $table->longText('privacy_policy')->nullable();
            $table->longText('refund_and_cancellation_policy')->nullable();
        });

        // Seed default English compliance texts into the settings table
        $defaultTerms = <<<'EOD'
# Terms and Conditions

**Effective Date:** June 2, 2026  
**Platform Owner:** Mohamed Soliman  
**Website/Platform Name:** EDU School  

Welcome to **EDU School**. These Terms and Conditions ("Terms") govern your access to and use of our online educational platform, including all courses, video content, quizzes, and digital materials provided on our website. By registering an account or purchasing any service, you agree to comply with and be bound by these Terms.

---

### 1. Acceptance of Terms
By accessing or using EDU School, you represent that you are at least 18 years of age (or have parental/guardian consent if you are a minor) and possess the legal authority to enter into this agreement. If you do not agree to these Terms, you must not access or use the platform.

### 2. User Account Creation
* **Registration:** To access most features and courses, you must register for an account. You agree to provide accurate, current, and complete information during the registration process.
* **Account Verification:** We reserve the right to verify user details (including name, email, and phone number) to ensure the integrity of the platform.
* **Accuracy:** You are solely responsible for keeping your profile information up to date.

### 3. Account Security & Responsibilities
* **Credential Safety:** You are responsible for safeguarding your password and account credentials. You must not disclose your password to any third party.
* **Unauthorized Use:** You must immediately notify EDU School of any breach of security or unauthorized use of your account.
* **Liability:** Mohamed Soliman and EDU School shall not be liable for any losses caused by any unauthorized use of your account. You may be held liable for losses incurred by the platform or others due to someone else using your account.

### 4. Intellectual Property & Copyright Protection
All content on EDU School—including video lectures, quizzes, PDFs, slides, graphics, software, and text—is the exclusive property of Mohamed Soliman and is protected by local Egyptian copyright laws, regional Arab conventions, and international intellectual property treaties.
* **Strict Licensing Limit:** Upon purchasing a course, you are granted a limited, personal, non-exclusive, non-transferable, and revocable license to view the content for personal educational purposes.
* **No Account Sharing:** Your account is strictly personal. Sharing your account credentials with anyone else to allow them access to courses is strictly prohibited.
* **No Recording or Redistribution:** You are strictly prohibited from:
  * Using screen recorders, capture software, or external cameras to record, copy, or capture video lessons.
  * Downloading, copying, translating, publishing, or redistributing any platform content in any form (including Telegram channels, YouTube, social media, or cloud storage).
* **Legal Action & Account Termination:** Any detected account sharing, screen recording, or unauthorized redistribution will result in **immediate termination of your account without a refund** and may expose you to civil and criminal liability under Egyptian Cybercrime and Intellectual Property Protection Laws.

### 5. Disclaimer of Warranties
All courses and content are provided "as is" and "as available" without warranties of any kind. While we strive to provide high-quality educational content, we do not guarantee specific academic, professional, or financial outcomes from taking our courses.

### 6. Modifications to Terms
We reserve the right to modify these Terms at any time. Any changes will be posted on this page with an updated effective date. Continued use of the platform after such modifications constitutes your acceptance of the revised Terms.

### 7. Governing Law & Jurisdiction
These Terms shall be governed by and construed in accordance with the laws of the Arab Republic of Egypt. Any disputes arising out of or in connection with these Terms shall be subject to the exclusive jurisdiction of the competent courts in Cairo, Egypt.

### 8. Contact Information
For any questions regarding these Terms and Conditions, please contact us at support@eduschool.com.
EOD;

        $defaultPrivacy = <<<'EOD'
# Privacy Policy

**Effective Date:** June 2, 2026  
**Platform Owner:** Mohamed Soliman  
**Website/Platform Name:** EDU School  

At **EDU School**, accessible from our platform, one of our main priorities is the privacy of our visitors and students. This Privacy Policy document outlines the types of information we collect, how we protect it, and our commitment to payment security.

---

### 1. Information We Collect
We collect personal information that you voluntarily provide to us when you register on the platform, make a purchase, or communicate with us. This information includes:
* **Name:** Used to personalize your account, issue certificates, and address you.
* **Email Address:** Used for account verification, login, course notifications, and support communication.
* **Phone Number:** Used for account security, verification, and essential updates.

### 2. How We Use Your Information
We use the collected information in the following ways:
* To provide, operate, and maintain our educational platform.
* To process your payments and verify course enrollments.
* To communicate with you, including responding to support requests.
* To secure accounts and prevent fraudulent activities.
* To comply with legal and regulatory obligations.

### 3. Payment Processing & Security Disclaimer
We take your financial security very seriously. 
* **Payment Disclaimer:** We do not store or process credit card or mobile wallet details on our servers. All financial transactions are processed securely via our integrated, certified payment gateway, Paymob.
* During the checkout process, you are redirected to a secure Paymob portal. Paymob handles all credit/debit card entries and mobile wallet transactions in compliance with the Payment Card Industry Data Security Standard (PCI-DSS).

### 4. Data Security and Protection
We implement robust administrative, technical, and physical security measures to protect your personal data from unauthorized access, alteration, disclosure, or destruction. This includes the use of Secure Socket Layer (SSL) encryption for all data transfers and secure cloud database protocols.

### 5. Third-Party Sharing
We do not sell, trade, or rent your personal information to third parties. We only share data with trusted third-party service providers (such as hosting servers and payment gateways) who assist us in operating our platform, provided they agree to keep this information confidential and secure.

### 6. Cookies
Like any other website, EDU School uses "cookies". These cookies are used to store information, including visitors' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users' experience by customizing our web page content based on visitors' browser type and/or other information.

### 7. Your Rights
Under applicable data protection laws in Egypt and the region, you have the right to:
* Access the personal data we hold about you.
* Request corrections to inaccurate data.
* Request the deletion of your account and associated personal data, subject to legal retention obligations.

### 8. Changes to This Privacy Policy
We may update our Privacy Policy from time to time. We advise you to review this page periodically for any changes. Changes are effective immediately once posted.

### 9. Contact Us
If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us at support@eduschool.com.
EOD;

        $defaultRefund = <<<'EOD'
# Refund and Cancellation Policy

**Effective Date:** June 2, 2026  
**Platform Owner:** Mohamed Soliman  
**Website/Platform Name:** EDU School  

Thank you for choosing **EDU School** for your educational journey. Please read this policy carefully to understand your rights regarding refunds and cancellations.

---

### 1. General Principles
Our courses, quizzes, and educational content are digital assets that are delivered instantly upon purchase. Because digital content is consumable and copyable immediately upon access, we maintain strict rules regarding refunds.

### 2. Refund Eligibility
Due to the digital nature of our products, all sales are final and non-refundable. Once a course is purchased and access to the video lessons or course materials is granted on your dashboard, you are not eligible for a refund. We encourage students to watch free promotional videos or review the course outline before completing a purchase.

### 3. Approved Refund Methods and Timelines
* **Refund Method:** If your refund is approved under our conditions, the funds will be issued back exclusively to the original payment method (credit card, debit card, or mobile wallet) used during the initial transaction.
* **Timeline:** Please note that approved refunds may take between five (5) to fourteen (14) business days to reflect in your account, depending on your bank's policies and payment gateway (Paymob) processing timelines. We have no control over bank processing delays.

### 4. How to Request a Refund (If Eligible)
If you qualify for a refund, you must submit a formal request:
1. Send an email to support@eduschool.com.
2. Include your full name, registered phone number, order number, and the reason for your refund request.
3. Our support team will review your account log to verify the purchase date and course consumption rate. You will be notified of the approval or rejection of your refund within 3 business days.

### 5. Policy Abuse
We monitor refund requests closely. If we suspect that a user is abusing this policy (e.g., buying and requesting refunds on multiple courses after downloading materials), we reserve the right to deny the request, suspend the user account, and block them from future purchases.
EOD;

        // If settings record exists, update it with default compliance texts
        DB::table('settings')->update([
            'terms_and_conditions' => $defaultTerms,
            'privacy_policy' => $defaultPrivacy,
            'refund_and_cancellation_policy' => $defaultRefund,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'terms_and_conditions',
                'privacy_policy',
                'refund_and_cancellation_policy'
            ]);
        });
    }
};
