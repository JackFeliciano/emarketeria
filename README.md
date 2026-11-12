# Emarketeria

## What the project is

Emarketeria is a network where brands and affiliates with an audience find each other. A brand opens a campaign with a product, an order value, a commission rate and a follower band. An affiliate joins the campaigns open to their size and earns a commission on what they sell. The network holds that commission, tracks it and pays it out.

The code turns that flow into a set of classes. A Campaign object holds its id, the brand, the product, the order value in cents, the commission in basis points and the follower band from low to high. A Creator object holds the handle, the follower count and the niche. A Sale object holds one sale: the campaign id, the handle, the order value in cents and the reversal flag. The Network class adds campaigns and creators, matches a creator to the campaigns open to their band and records each sale. The class works out the commission as the order value times the basis points and rounds the result down to whole cents. The class sums a payout across every live sale of one creator. The class reports the paid orders and works out the network's own share. A reversed sale pays nothing and leaves the paid orders.

The page route serves the demo network as HTML. The probe route reports health data in JSON. Sample.php builds the demo network with four campaigns. The suite runs 18 checks.

## How to run it

The demo runs on the PHP built-in server. This command starts the server from the repository root:

```
php -S 127.0.0.1:8112 -t showroom showroom/index.php
```

The router reads the base path from the EMARKETERIA_BASE environment variable. The variable matters when the app runs under a subpath. The block below shows a typical value:

```
EMARKETERIA_BASE=/emarketeria
```

The hand-written runner starts the suite. This command runs the checks:

```
php tests/run.php
```

## What the routes answer

The page route is /. The page route returns the HTML view of the demo network. The probe route is /health. The probe route returns JSON with the fields ok, campaigns, sales and paid.

## How the layout is arranged

autoload.php loads the classes. The src/Home/ directory holds the model classes Campaign, Creator, Sale, Network and Sample. The src/App/ directory holds the router, the front controller and the HTML. The showroom/ directory holds the demo entry file and the stylesheet. The tests/ directory holds the hand-written runner, the assertion helper and the test file. The deploy/ directory holds the systemd unit and the nginx location block.

## The state of the suite

The suite runs 18 checks. The hand-written runner drives the checks. The assertion helper backs each check. This command starts the suite: `php tests/run.php`.

## Licence

The repository uses the Mozilla Public License 2.0. The licence lets you use the code, change it and share it. The licence lets you combine these files with code under other terms. The licence requires that you share any changed file under the same licence.
