<?php
   /*
   Plugin Name: Onitway HubSpot Coustom From 
   description: HubSpot custome contact from with 2 step from
   Version: 1.0
   Author: Onitway
   License: GPL2
   */


    // function hubspot contact from
    function wpb_hubspot_contact_form_shortcode() {
        $message = 'Hello world!';
        return $message;
    }

    // register shortcode hubspot contact from
    add_shortcode('hubspot_contact_form', 'wpb_hubspot_contact_form_shortcode');

?>


<section class="multi-step-form">
	
	<h1>Multi-step form</h1>
	
	<div class="steps">
		<button class="active" type="button" disabled>Step One</button> | 
		<button type="button" disabled>Step Three</button>
	</div>
	
	<form action="#" method="post">
		
		<fieldset aria-label="Step One" tabindex="-1" id="step-1">
			<h2>Step One</h2>
			<p>
				<label for="first-name">First Name</label>
				<input class="form-control" type="text" name="first-name" id="first-name" required>
			</p>
			<p>
				<label for="last-name">Last Name</label>
				<input class="form-control" type="text" name="last-name" id="last-name" required>
			</p>
            <p>
				<label for="email-address">Email Address</label>
				<input class="form-control" type="email" name="email-address" id="email-address" required>
			</p>
			<p>
				<button class="btn btn-default btn-next" type="button" aria-controls="step-2">Next</button>
			</p>
		</fieldset>
		
		<fieldset aria-label="Step Two" tabindex="-1" id="step-2">
			<h2>Step Two</h2>
            <p>
				<label for="phone-number">Phone Number <span class="optional">(optional)</span></label>
				<input class="form-control" type="tel" name="phone-number" id="phone-number">
			</p>
			<p>
				<label for="message"></label>
				<textarea class="form-control" rows="3" name="message" id="message" required></textarea>
			</p>
			<p>
				<button class="btn btn-success" type="submit">Submit</button> 
				<button class="btn btn-default btn-edit" type="button">Edit</button> 
				<button class="btn btn-danger" type="reset">Start Over</button>
			</p>
		</fieldset>
		
	</form>
</section>


<!-- 
HTML Requirements

1. Include .multi-step-form
2. Define parents e.g. fieldset
3. Each parent should own a unique ID with aria-label
4. Each next/prev button owns aria-controls which points to unique ID of parents
5. For validation each field should own required attribute

<section class="multi-step-form">
	<form>

		<fieldset aria-label="Step One" tabindex="-1" id="step-1">
			...
			<button class="btn-next" type="button" aria-controls="step-2">Next</button>
		</fieldset>

		<fieldset aria-label="Step Two" tabindex="-1" id="step-2">
			...
			<button class="btn-prev" type="button" aria-controls="step-1">Previous</button>
			<button class="btn-next" type="button" aria-controls="step-3">Next</button>
		</fieldset>

		<fieldset aria-label="Step Three" tabindex="-1" id="step-3">
			...
			<button type="submit">Submit</button>
			<button class="btn-edit" type="button">Edit</button>
			<button type="reset">Start Over</button>
		</fieldset>

	</form>
</section>
-->