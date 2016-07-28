<?php

namespace app\models\oauth2;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\oauth2\Oauth2Client;

/**
 * Oauth2ClientSearch represents the model behind the search form about `app\models\oauth2\Oauth2Client`.
 */
class Oauth2ClientSearch extends Oauth2Client {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['client', 'client_secret', 'scopes', 'redirect_uris', 'logout_uri'], 'safe'],
			[['created_at', 'updated_at'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = Oauth2Client::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'client', $this->client])
			->andFilterWhere(['like', 'client_secret', $this->client_secret])
			->andFilterWhere(['like', 'scopes', $this->scopes])
			->andFilterWhere(['like', 'redirect_uris', $this->redirect_uris])
			->andFilterWhere(['like', 'logout_uri', $this->logout_uri]);

		return $dataProvider;
	}
}
