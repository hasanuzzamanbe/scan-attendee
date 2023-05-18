function Bomb(x, type, speed, width) {
    this.selectedImage = productImages[Math.floor(Math.random() * productImages.length)]
    this.origin = x;
    this.position = createVector(0, 0);
    this.serpentine = random(3) + 4;
    this.squashed = false;
    this.radius = 60; // size of bug
    this.speed = speed;
    this.onLeaving = false;
    this.width = width;
    this.type = 'bomb'
    this.showingFullAnimation = false;

    this.frames = (bombSpriteData.frames)
    this.bombAnimation = [];

    if(bombAnimation1 == null){
        bombAnimation1 = [];
        for (let i = 0; i < this.frames.length; i++) {
            let pos = this.frames[i].frame;
            let img = bombSpriteSheet.get(pos.x, pos.y, pos.w, pos.h);
            bombAnimation1.push(img);
        }
    }

    if(bombAnimation2 == null){
        bombAnimation2 = [];
        for (let i = 0; i < 3; i++) {
            let pos = this.frames[i].frame;
            let img = bombSpriteSheet.get(pos.x, pos.y, pos.w, pos.h);
            bombAnimation2.push(img);
        }
    }
    this.bombAnimation = new Sprite(
        bombAnimation2,
        this.position.x - (this.radius/2),
        this.position.y - (this.radius /2),
        0.4,
        this.radius);
}

Bomb.prototype.draw = function() {
    if(this.squashed && !this.showingFullAnimation){
        this.showingFullAnimation = true
        this.bombAnimation = new Sprite(
            bombAnimation1,
            0,
            this.position.y - (canvasWidth /2),
            0.4,
            canvasWidth);
    }

    if( !this.showingFullAnimation){
        this.bombAnimation.updateCoordinate(
            this.position.x - (this.radius/2),
            this.position.y - (this.radius /2),
        )
    }


    this.bombAnimation.show()
    this.bombAnimation.animate()
};


Bomb.prototype.update = function(isBombExploded) {
    this.position.y += this.speed;
    this.position.x = cos(this.position.y * (0.005 * this.serpentine) + this.serpentine * 10) * (this.width / this.serpentine) + this.origin;
}

Bomb.prototype.squashedBy = function(x, y) {
    var d = dist(x, y, this.position.x, this.position.y);
    return (d < this.radius);
};

Bomb.prototype.isBugOutOfScreen = function (){
    return this.position.y+this.radius > canvasHeight
}