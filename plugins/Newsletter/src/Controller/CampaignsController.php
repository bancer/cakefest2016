<?php
namespace Newsletter\Controller;

use Newsletter\Controller\AppController;

/**
 * Campaigns Controller
 *
 * @property \Newsletter\Model\Table\CampaignsTable $Campaigns
 */
class CampaignsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Templates']
        ];
        $campaigns = $this->paginate($this->Campaigns);

        $this->set(compact('campaigns'));
        $this->set('_serialize', ['campaigns']);
    }

    /**
     * View method
     *
     * @param string|null $id Campaign id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $campaign = $this->Campaigns->get($id, [
            'contain' => ['Templates', 'MailingLists', 'Logs']
        ]);

        $this->set('campaign', $campaign);
        $this->set('_serialize', ['campaign']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $campaign = $this->Campaigns->newEntity();
        if ($this->request->is('post')) {
            $campaign = $this->Campaigns->patchEntity($campaign, $this->request->data);
            if ($this->Campaigns->save($campaign)) {
                $this->Flash->success(__('The campaign has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The campaign could not be saved. Please, try again.'));
            }
        }
        $templates = $this->Campaigns->Templates->find('list', ['limit' => 200]);
        $mailingLists = $this->Campaigns->MailingLists->find('list', ['limit' => 200]);
        $this->set(compact('campaign', 'templates', 'mailingLists'));
        $this->set('_serialize', ['campaign']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Campaign id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $campaign = $this->Campaigns->get($id, [
            'contain' => ['MailingLists']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $campaign = $this->Campaigns->patchEntity($campaign, $this->request->data);
            if ($this->Campaigns->save($campaign)) {
                $this->Flash->success(__('The campaign has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The campaign could not be saved. Please, try again.'));
            }
        }
        $templates = $this->Campaigns->Templates->find('list', ['limit' => 200]);
        $mailingLists = $this->Campaigns->MailingLists->find('list', ['limit' => 200]);
        $this->set(compact('campaign', 'templates', 'mailingLists'));
        $this->set('_serialize', ['campaign']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Campaign id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $campaign = $this->Campaigns->get($id);
        if ($this->Campaigns->delete($campaign)) {
            $this->Flash->success(__('The campaign has been deleted.'));
        } else {
            $this->Flash->error(__('The campaign could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function queries()
    {
        $query = $this->Campaigns->find()
            ->select(['Campaigns.name', 'Campaigns.status'])
            ->where(['Campaigns.status !=' => 'new'])
            ->order(['Campaigns.name' => 'asc'])
            ->limit(10);

        debug($query->all());
        debug($query->toArray());
        debug($query->count());
        debug($query->first());

        $this->render(false);
    }

    public function contain()
    {
        $query = $this->Campaigns->find()
            //->contain('MailingLists.Users');
            ->contain(['MailingLists.Users' => [
                'conditions' => ['email LIKE' => 'updates%']
            ]]);
        debug($query->toArray());
        $this->render(false);
    }

    public function saveExample()
    {
        $campaign = $this->Campaigns->newEntity();
        $campaign->name = 'New Campaign';
        $campaign->status = 'new';
        debug($campaign->errors());
        debug($this->Campaigns->save($campaign));

        $campaign->template_id = 1;
        debug($this->Campaigns->save($campaign));

        $this->render(false);
    }
}
