 // get the Apply preferences button
 const applyButton = document.querySelector('.applyPreferencesButton');

 // add a click event listener to the button
 applyButton.addEventListener('click', function() {
     // get the selected preferences
     const date = document.getElementById('date').value;
     const time = document.getElementById('time').value;
     const language = document.getElementById('language').value;
     const ticket = document.getElementById('ticket').value;

     // extract the numeric price value from the selected ticket value
     const selectedPrice = ticket.match(/\d+/)[0];

     // get all the tickets
     const tickets = document.querySelectorAll('.ticket');

     // loop through each ticket
     tickets.forEach(function(ticket) {
         // get the ticket's properties
         const ticketDate = ticket.querySelector('.ticket-info > p:nth-child(3)').textContent.trim();
         const ticketTime = ticket.querySelector('.ticket-info > p:nth-child(3)').textContent.trim();
         const ticketLanguage = ticket.querySelector('.ticket-info > p:nth-child(2)').textContent.trim();
         const ticketPrice = ticket.querySelector('.ticket-price > p').textContent.trim();
         const ticketNumericPrice = ticketPrice.match(/\d+/)[0];

         // check if the ticket matches the selected preferences
         const dateMatch = date === 'Choose Date' || ticketDate === date;
         const timeMatch = time === 'Choose Time' || ticketTime === time;
         const languageMatch = language === 'Select language' || ticketLanguage === language;
         const priceMatch = selectedPrice === ticketNumericPrice;

         // if the ticket matches, display it. Otherwise, hide it.
         if (priceMatch) {
             ticket.style.display = 'block';
         } else {
             ticket.style.display = 'none';
         }

         console.log(ticketTime);
     });
 });