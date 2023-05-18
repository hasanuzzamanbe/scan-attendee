class GameScreen {
    bugs = {}
    bugIndex = 1;
    bugChance = 0.8;
    score = 0;
    speed = 1;
    fraction = 15
    lifeCount = 3;
    onGameOver = null

    constructor(onGameOver) {
        clear();
        this.onGameOver = onGameOver
    }

    draw (){

        this.drawScoreAndLife()
        this.calculateSpeedAndChance()
        if(Object.keys(this.bugs).length < 1 || true){
            this.addNewElement();
        }
        let ref = this;
        Object.keys(this.bugs).map((index)=> {
            let bug = this.bugs[index];
            if(bug.isBugOutOfScreen()){
                if(this.isProduct(bug)){
                    this.lifeCount = this.lifeCount>0? this.lifeCount-1 : 0;
                }
                if(this.lifeCount === 0){
                    ref.setGameOver()

                    if(ref.onGameOver != null){
                        ref.onGameOver(ref.score)
                    }
                }
                delete this.bugs[index];

            }else{
                bug.draw();
                bug.update();
            }
        })
    }

    setGameOver(){

    }

    drawScoreAndLife(){
        let heartSize = 15
        let heartY = 10

        fill('#fff');
        textSize(20)
        text('Score: '+this.score, 50 , 20)
        for (let x = 0; x < 3; x ++) {
            this.heart(canvasWidth -( (x+1) *( heartSize+5)), heartY, heartSize);
        }
        for (let x = 0; x < this.lifeCount; x ++) {
            fill('#FF0000');
            this.heart(canvasWidth -( (x+1) *( heartSize + 5)), heartY, heartSize);
        }
    }

     heart(x, y, size) {
        beginShape();
        vertex(x, y);
        bezierVertex(x - size / 2, y - size / 2, x - size, y + size / 3, x, y + size);
        bezierVertex(x + size, y + size / 3, x + size / 2, y - size / 2, x, y);
        endShape(CLOSE);
    }


    
    // frameCount
    addNewElement() {
        if (frameCount % this.fraction === 0) { // every second
            if (random() < this.bugChance) { // probability of a new bug
                var x = random(canvasWidth / 2) + canvasWidth / 4; // only in the middle
                var type = (random() > 0.6);
                const logo = type ? new Bomb(x, type, this.speed, canvasWidth): new Product(x, type, this.speed, canvasWidth);

                this.bugs[this.bugIndex] = logo
                this.bugIndex++;
            }
        }
    }

    calculateSpeedAndChance() {
        if (frameCount % this.fraction === 0) {
            this.bugChance = map(this.score, 0, 500, 0.4, 0.999);
            this.speed = map(this.score, 0, 500, 3, 30);
        }
    }
    handelTap (){
        let ref = this;
        Object.keys(this.bugs).map((index)=>{
            let bug = this.bugs[index];

            if(bug.onLeaving){
                return
            }

            bug.squashed = bug.squashedBy(mouseX, mouseY);

            if (bug.squashed) {
                bug.onLeaving = true;
                if(ref.isProduct(bug)){
                    this.score++;
                    setTimeout(()=>{
                        delete this.bugs[index];
                    }, 520)
                }else{
                    setTimeout(()=>{
                        delete this.bugs[index];
                        ref.setGameOver()
                        if(ref.onGameOver != null){
                            ref.onGameOver(ref.score)
                        }
                    }, 590)
                }
            }
        })
    }

    isProduct(bug){
        return typeof bug.type != 'undefined' && bug.type === 'product';
    }
}