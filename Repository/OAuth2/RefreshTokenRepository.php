<?php

namespace Plugin\EccubeApi\Repository\OAuth2;

use Doctrine\ORM\EntityRepository;
use Plugin\EccubeApi\Entity\OAuth2\RefreshToken;
use OAuth2\Storage\RefreshTokenInterface;

/**
 * RefreshTokenRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @link http://bshaffer.github.io/oauth2-server-php-docs/cookbook/doctrine2/
 */
class RefreshTokenRepository extends EntityRepository implements RefreshTokenInterface
{
    public function getRefreshToken($token)
    {
        $refreshToken = $this->findOneBy(array('refresh_token' => $token));
        if ($refreshToken) {
            $Client = $refreshToken->getClient();
            $User = $refreshToken->getUser();
            $refreshToken = $refreshToken->toArray();
            $refreshToken['expires'] = $refreshToken['expires']->getTimestamp();
            if ($Client) {
                $refreshToken['client_id'] = $Client->getClientIdentifier();
            } else {
                $refreshToken['client_id'] = null;
            }
            if ($User) {
                $refreshToken['user_id'] = $User->getId();
            } else {
                $refreshToken['user_id'] = null;
            }
        }
        return $refreshToken;
    }

    public function setRefreshToken($refreshToken, $clientIdentifier, $userEmail, $expires, $scope = null)
    {
        $client = $this->_em->getRepository('Plugin\EccubeApi\Entity\OAuth2\Client')
            ->findOneBy(
                array('client_identifier' => $clientIdentifier)
            );
        $user = $this->_em->getRepository('Plugin\EccubeApi\Entity\OAuth2\User')
            ->findOneBy(
                array('email' => $userEmail)
            );
        $RefreshToken = new \Plugin\EccubeApi\Entity\OAuth2\RefreshToken();
        $RefreshToken->setPropertiesFromArray(array(
           'refresh_token'  => $refreshToken,
           'client'         => $client,
           'user'           => $user,
           'expires'        => (new \DateTime())->setTimestamp($expires),
           'scope'          => $scope,
        ));
        $this->_em->persist($RefreshToken);
        $this->_em->flush();
    }

    public function unsetRefreshToken($refreshToken)
    {
        $refreshToken = $this->findOneBy(array('refresh_token' => $refreshToken));
        $this->_em->remove($refreshToken);
        $this->_em->flush();
    }
}
