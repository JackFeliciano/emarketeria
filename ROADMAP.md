# Roadmap

The project today holds the model classes in src/Home/ and the router, the front controller and the HTML in src/App/. The demo sits in showroom/, the suite in tests/ and the deploy files in deploy/. The network matches a creator to the open campaigns and records each sale. The network works out each commission, sums each payout and reports its own share. The suite runs 18 checks. The list below names five improvements a contributor could make.

1. The Network class holds every campaign, creator and sale in memory. A restart loses the data. A contributor could add a store class that writes the model to a database. The work would touch src/Home/Network.php and a new file under src/Home/.

2. The Network class matches a creator to campaigns using the follower band alone. The niche field on a creator plays no part in the pairing. A contributor could let a campaign name the niches it accepts. The work would touch src/Home/Campaign.php and the matching code in src/Home/Network.php.

3. The network sums a payout across the live sales of one creator. The network records no payout event. A contributor could add a payout record that marks a sum as sent. The work would touch src/Home/Network.php and a new payout class under src/Home/.

4. The Sale object holds the reversal as a flag. No route flips that flag. A contributor could add a route that reverses a recorded sale. The work would touch the router in src/App/ and the sale handling in src/Home/Network.php.

5. The page route shows the whole demo network in one view. A contributor could add query parameters that narrow the page to one creator or one campaign. The work would touch the front controller in src/App/ and the HTML.
