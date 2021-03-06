<?php

namespace App\EntryPoint\Controller\Site\ShoppingCart;

use App\Application\Cart\Query\GetCart\GetCartQuery;
use App\Application\Cart\Query\GetCart\GetCartQueryHandler;
use App\Domain\Model\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class CartController extends AbstractController
{
    private GetCartQueryHandler $cartQueryHandler;

    public function __construct(GetCartQueryHandler $cartQueryHandler)
    {
        $this->cartQueryHandler = $cartQueryHandler;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        $cartId = $request->getSession()->get('cartId');
        if($cartId == null){
            $cartId = UuidV4::v4()->jsonSerialize();
            $request->getSession()->set('cartId', $cartId);
        }
        /** @var User $user */
        $user = $this->getUser();
        if($user == null){
            return $this->redirectToRoute('app_login');
        }
        $cart = $this->cartQueryHandler->handle(new GetCartQuery($cartId,$user->getId()));
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }
}
